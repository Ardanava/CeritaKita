
const Delta = Quill.import('delta');

const MAX_FILE_SIZE_KB = 250;
const MAX_FILE_SIZE = MAX_FILE_SIZE_KB * 1024; 
const quill = new Quill('#editor', {
    theme: 'snow',
    modules: {
        toolbar: [
            ['bold', 'italic', 'underline'],
            [{ 'align': [] }], 
            ['link', 'image', 'blockquote']
        ],
        clipboard: {
            matchers: [
                ['img', (node, delta) => {
                    const src = node.getAttribute('src');
                    
                    if (src && src.startsWith('data:image/')) {
                        const file = dataURLtoFile(src, 'pasted_image.png');

                        if (file && file.size > MAX_FILE_SIZE) {
                            const sizeInKB = (file.size / 1024).toFixed(0);
                            alert(`Gambar terlalu besar (${sizeInKB} KB). Batas maksimal ${MAX_FILE_SIZE_KB} KB.`);
                            return new Delta(); 
                        }
                        
                        return delta;
                    }
                    
                    return delta;
                }]
            ]
        }
    }
});

if (window.oldContent) {
    quill.root.innerHTML = window.oldContent;
}

quill.getModule('toolbar').addHandler('image', () => {
    selectLocalImage();
});

function selectLocalImage() {
    const input = document.createElement('input');
    input.setAttribute('type', 'file');
    input.setAttribute('accept', 'image/*');
    input.click();

    input.onchange = () => {
        const file = input.files[0];
        
        if (!file || !/^image\//.test(file.type)) {
            alert('Harap pilih file gambar (jpg, png, gif).');
            return;
        }

        if (file.size > MAX_FILE_SIZE) {
            const sizeInKB = (file.size / 1024).toFixed(0);
            alert(`Ukuran file terlalu besar (${sizeInKB} KB). Maksimal ${MAX_FILE_SIZE_KB} KB.`);
            return; 
        }
        const reader = new FileReader();
        
        reader.onload = (e) => {
            insertImageToEditor(e.target.result);
        };
        reader.readAsDataURL(file);
    };
}

function insertImageToEditor(dataUrl) {
    const range = quill.getSelection(true);
    let index = range ? range.index : 0;
    quill.insertEmbed(index, 'image', dataUrl);
    quill.setSelection(index + 1);
}

const form = document.querySelector('#post-form');
if (form) {
    form.addEventListener('submit', function(e) {
        const contentInput = document.getElementById('content-input');
        if (contentInput) {
            let htmlContent = quill.root.innerHTML;
            if (htmlContent === '<p><br></p>') {
                htmlContent = '';
            }
            contentInput.value = htmlContent;
        } else {
            console.error('Input #content-input tidak ditemukan!');
            e.preventDefault();
        }
    });
} else {
    console.warn('Form #post-form tidak ditemukan!');
}

function dataURLtoFile(dataUrl, filename) {
    const arr = dataUrl.split(',');
    const mime = arr[0].match(/:(.*?);/)[1];
    const bstr = atob(arr[1]);
    let n = bstr.length;
    const u8arr = new Uint8Array(n);
    while (n--) {
        u8arr[n] = bstr.charCodeAt(n);
    }
    return new File([u8arr], filename, { type: mime });
}