export const Ziggy = {
    routes: {},
    url: '',
    port: null
}

if (import.meta.env.DEV) {
    fetch('/ziggy')
        .then(response => response.json())
        .then(routes => {
            Object.assign(Ziggy, routes)
        })
}
