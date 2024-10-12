export const isImage = (attachment) => {
    let mime = attachment.mime || attachment.type
    mime = mime.split('/')
    return mime[0].toLowerCase() === 'image'
}

export const isVideo = (attachment) => {
    let mime = attachment.mime || attachment.type
    mime = mime.split('/')
    return mime[0].toLowerCase() === 'video'
}

export const isAudio = (attachment) => {
    let mime = attachment.mime || attachment.type
    mime = mime.split('/')
    return mime[0].toLowerCase() === 'audio'
}

export const isPdf = (attachment) => {
    let mime = attachment.mime || attachment.type
    return mime.toLowerCase() === 'application/pdf'
}
