/**
 * Quill fix getHtml
 */
Quill.prototype.getHtml = function() {
    return this.container.querySelector(".ql-editor").innerHTML;
};

/**
 * Wrap Class Image Quill
 */
var ImageQuill = Quill.import("formats/image");
ImageQuill.className = "img-quill";
Quill.register(ImageQuill, true);

/**
 *  Upload Image to Server
 */
function setupBaseQuill(quill, name) {
    quill.on("editor-change", function(eventName, ...args) {
        if (eventName === "text-change") {
            $("input[name='" + name + "']").val(quill.getHtml());
        }
    });

    quill.on("text-change", async function(delta, oldDelta, source) {
        const imgs = Array.from(
            quill.container.querySelectorAll('img[src^="data:"]:not(.loading)')
        );

        for (const img of imgs) {
            img.classList.add("loading");

            img.setAttribute(
                "src",
                await uploadBase64Img(img.getAttribute("src"))
            );

            img.classList.remove("loading");
        }
    });
}

// wait for upload
async function uploadBase64Img(base64Str) {
    if (typeof base64Str !== "string" || base64Str.length < 100) {
        return base64Str;
    }
    const url = await b64ToUrl(base64Str);
    return url;
}

/**
 * Convert a base64 string in a Blob according to the data and contentType.
 *
 * @param b64Data {String} Pure base64 string without contentType
 * @param contentType {String} the content type of the file i.e (image/jpeg - image/png - text/plain)
 * @param sliceSize {Int} SliceSize to process the byteCharacters
 * @see http://stackoverflow.com/questions/16245767/creating-a-blob-from-a-base64-string-in-javascript
 * @return Blob
 */
function b64toBlob(b64Data, contentType, sliceSize) {
    contentType = contentType || "";
    sliceSize = sliceSize || 512;

    var byteCharacters = atob(b64Data);
    var byteArrays = [];

    for (var offset = 0; offset < byteCharacters.length; offset += sliceSize) {
        var slice = byteCharacters.slice(offset, offset + sliceSize);

        var byteNumbers = new Array(slice.length);
        for (var i = 0; i < slice.length; i++) {
            byteNumbers[i] = slice.charCodeAt(i);
        }

        var byteArray = new Uint8Array(byteNumbers);

        byteArrays.push(byteArray);
    }

    var blob = new Blob(byteArrays, { type: contentType });
    return blob;
}

function b64ToUrl(base64) {
    return new Promise(resolve => {
        var block = base64.split(";");
        var contentType = block[0].split(":")[1];
        var realData = block[1].split(",")[1];
        var blob = b64toBlob(realData, contentType);

        const fd = new FormData();
        fd.append("file_upload", blob);

        const xhr = new XMLHttpRequest();

        xhr.open("POST", "/admin/sobre/quillUpload", true);
        xhr.setRequestHeader(
            "X-CSRF-TOKEN",
            $('meta[name="csrf-token"]').attr("content")
        );

        xhr.onload = () => {
            if (xhr.status === 200) {
                const url = JSON.parse(xhr.responseText).path;
                resolve(url);
            }
        };
        xhr.send(fd);
    });
}
