const div = document.createElement('div')
const textarea = document.createElement('textarea')

export function decodeHTMLEntities(str: string) {
    if (str && typeof str === 'string') {
        str = str.replace(/<script[^>]*>([\S\s]*?)<\/script>/gmi, '')
        str = str.replace(/<\/?\w(?:[^"'>]|"[^"]*"|'[^']*')*>/gmi, '')
        div.innerHTML = str
        str = div.textContent!
        div.textContent = ''
    }
    return str
}

export function copyToClipboard(str: string) {
    textarea.value = str;

    textarea.style.top = '0'
    textarea.style.left = '0'
    textarea.style.position = 'fixed'

    document.body.appendChild(textarea)

    textarea.focus();
    textarea.select();

    try {
        var successful = document.execCommand('copy');
        var msg = successful ? 'successful' : 'unsuccessful';
        console.log('Fallback: Copying text command was ' + msg);
    } catch (err) {
        console.error('Fallback: Oops, unable to copy', err);
    }

    document.body.removeChild(textarea)
}