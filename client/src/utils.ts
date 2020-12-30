const div = document.createElement('div');

export function decodeHTMLEntities(str: string) {
    if (str && typeof str === 'string') {
        // strip script/html tags
        str = str.replace(/<script[^>]*>([\S\s]*?)<\/script>/gmi, '');
        str = str.replace(/<\/?\w(?:[^"'>]|"[^"]*"|'[^']*')*>/gmi, '');
        div.innerHTML = str;
        str = div.textContent!;
        div.textContent = '';
    }
    return str;
}