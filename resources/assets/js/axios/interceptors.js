import axios from "axios";

axios.interceptors.request.use(
    (config) => {
        // dont send xsrf tokens on get requests, prevents preflight requests
        // header is necessary for oauth requests, hmmmmmmm
        if (config.method == "get" || config.removeXsrfToken)
            config.xsrfCookieName = undefined;
        return config;
    },
    (err) => {
        throw err;
    }
);
