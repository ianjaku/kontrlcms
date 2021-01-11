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
    }).then(response => response.json())
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
                console.log("test2");
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

/**
 *
 * @param data names & pages for the requered snippets format [{name: "", page: ""}]
 * @returns {Promise<Response>} returns an array of found snippets of format [{name: "", page: "", value: "", id: ""}]
 *                              if a snippet does not exist it is not shown in the list
 */
export function fetchSnippets(data) {
    const jsonData = JSON.stringify(data);
    const snippetQuery = encodeURI(jsonData);
    return fetch("/simplecms/snippets?snippets=" + snippetQuery)
            .then(r => r.json());
}

export default {
    uploadSnippetImage,
    uploadAnyImage,
    updateSnippet,
    fetchSnippets
}
