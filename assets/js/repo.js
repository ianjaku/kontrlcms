export function updateSnippet(name, value, page = PAGE_NAME) {
    return fetch('/simplecms/update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            page,
            name,
            value
        })
    })
}

export function uploadSnippetImage(name, imgFile, page = PAGE_NAME) {
    return new Promise((resolve) => {
        const formData = new FormData();
        formData.append('file', imgFile);
        formData.append('page', page);
        formData.append('name', name);

        fetch(`/simplecms/upload`, {
            method: "POST",
            body: formData
        }).then(response => {
            response.json().then(response => {
                resolve(response.url)
            })
        }).catch(err => {
            // TODO: Show an error or something
        })
    })
}

export function uploadAnyImage(imgFile, purpose) {
    return new Promise((resolve) => {
        const formData = new FormData();
        formData.append('file', imgFile);

        fetch(`/simplecms/upload/${purpose}`, {
            method: "POST",
            body: formData
        }).then(response => {
            response.json().then(response => {
                resolve(response.url)
            })
        }).catch(err => {
            // TODO: Show an error or smthng
        });
    });
}

export default {
    uploadSnippetImage,
    uploadAnyImage,
    updateSnippet
}
