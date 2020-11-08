/**
 * Makes ajax request using promise
 *
 * @param method {string} request method
 * @param url    {string} url where request should be made
 * @param data   {string} data for POST requests
 *
 * @return {object} Promise object
 */
function makeAjaxRequest(method, url, data = '')
{
    return new Promise(function (resolve, reject) {
        const xhr = new XMLHttpRequest();

        xhr.open(method, url);

        xhr.onreadystatechange = function() {
            if (xhr.readyState !== 4) return;

            if (xhr.status === 200) {
                resolve(xhr.responseText);
            } else {
                reject(xhr.status + ' ' + xhr.statusText);
            }
        }

        xhr.setRequestHeader('X-Requested-with', 'XMLHttpRequest');

        xhr.send(data);
    });
}
