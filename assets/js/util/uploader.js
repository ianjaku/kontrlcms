
export function uploadSnippetImage(imgFile, name, page, callback) {
    const formData = new FormData();
    formData.append('file', imgFile);
    formData.append('page', page);
    formData.append('name', name);

    fetch(`/simplecms/upload`, {
        method: "POST",
        body: formData
    }).then(response => {
        response.json().then(response => {
            callback(response.url)
        })
    }).catch(err => {
        // TODO: Show an error or smthng
    })
}

export function uploadAnyImage(imgFile, purpose, callback) {
    const formData = new FormData();
    formData.append('file', imgFile);

    fetch(`/simplecms/upload/${purpose}`, {
        method: "POST",
        body: formData
    }).then(response => {
        response.json().then(response => {
            callback(response.url)
        })
    }).catch(err => {
        // TODO: Show an error or smthng
    })
}