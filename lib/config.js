let config = {

    rootDir: null,

    $set: (key, val) => {
        config[key] = val;
        return config;
    }
}

module.exports = config;
