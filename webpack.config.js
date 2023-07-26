const fs = require("fs");
const glob = require("glob");
const path = require("path");
const webpack = require("webpack");
const SVGSpriter = require("svg-sprite");
const CopyPlugin = require("copy-webpack-plugin");
const { VueLoaderPlugin } = require("vue-loader");
const { EsbuildPlugin } = require("esbuild-loader");
const { CleanWebpackPlugin } = require("clean-webpack-plugin");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const { WebpackManifestPlugin } = require("webpack-manifest-plugin");
require("dotenv").config();

const JS_ASSET_PATH = process.env.JS_ASSET_PATH || "/";
const CSS_ASSET_PATH = process.env.CSS_ASSET_PATH || "/";

// https://github.com/fqborges/webpack-fix-style-only-entries/issues/42

let manifestFiles = {};

const BASE_SVG_DIR = "resources/assets/svgs";

const spriterConfig = {
    dest: `public/dist/js/sprites/`,
    mode: {
        symbol: {
            // TODO: change mode to css
            // TODO: can then inject the generated sprite css file into the site to generate as css
            // TODO: fixes issues with gradients, but then adds the annoyances with resizing those svgs back
            /*render: {
                scss: { dest: "_sprite.scss" },
            },*/
            // remove the copious amounts of nested folders it adds
            // WARNING: this will cause issues if other modes are used
            dest: "",
            sprite: "",
            bust: true,
        },
    },
};

module.exports = [
    {
        module: {
            rules: [
                {
                    test: /\.scss$/,
                    use: [
                        MiniCssExtractPlugin.loader,
                        "css-loader",
                        "sass-loader",
                    ],
                },
                {
                    test: /\.(png|jpe?g|gif|webp)(\?.*)?$/,
                    type: "asset/resource",
                    generator: {
                        filename: "images/[contenthash][ext]",
                    },
                },
                {
                    test: /\.(woff(2)?|ttf)(\?v=\d+\.\d+\.\d+)?$/,
                    type: "asset/resource",
                    generator: {
                        filename: "font/[contenthash][ext]",
                    },
                },
                {
                    test: /\.(svg)$/,
                    type: "asset/resource",
                    generator: {
                        filename: "sprites/[contenthash].svg",
                    },
                },
            ],
        },
        plugins: [
            new WebpackManifestPlugin({
                basePath: "/",
                fileName: "../../mix-manifest.json",
                generate: (seed, files, entries) => {
                    let mappedFiles = Object.assign(
                        ...files.map((file) => {
                            return { [file.name]: file.path };
                        })
                    );
                    manifestFiles = { ...manifestFiles, ...mappedFiles };
                    return manifestFiles;
                },
                map: (file) => {
                    if (file.name.endsWith("css"))
                        file.name = file.name.replace(".scss", "");
                    return file;
                },
            }),
            new CleanWebpackPlugin(),
            new MiniCssExtractPlugin({
                filename: "[contenthash].css",
            }),
        ],
        entry: {
            new_theme: ["./resources/assets/css/root.scss"],
            ...Object.assign(
                ...fs
                    .readdirSync("./resources/assets/css/legacy/themes")
                    .map((file) => {
                        return {
                            [file]: `./resources/assets/css/legacy/themes/${file}`,
                        };
                    })
            ),
        },
        optimization: {
            minimizer: [
                new EsbuildPlugin({
                    target: "es2015",
                    css: true,
                }),
            ],
        },
        output: {
            path: path.resolve(__dirname, "public/dist/css"),
            publicPath: CSS_ASSET_PATH,
        },
        resolve: {
            alias: {
                "@": path.resolve("./resources/assets"),
            },
        },
    },
    {
        module: {
            rules: [
                {
                    test: /\.(js|jsx)$/,
                    exclude: /node_modules/,
                    loader: "esbuild-loader",
                    options: {
                        target: "es2015",
                    },
                },
                {
                    test: /\.ts$/,
                    exclude: /node_modules/,
                    loader: "esbuild-loader",
                    options: {
                        loader: "ts",
                        target: "es2015",
                    },
                },
                {
                    test: /app\.ts$/,
                    loader: "string-replace-loader",
                    options: {
                        search: "sprite_sheets: {},",
                        strict: true,
                        replace(match, p1, offset, string) {
                            let finalFiles = {};
                            let files = glob.sync(`${BASE_SVG_DIR}/**/*.svg`);
                            let map = {};
                            for (let file of files) {
                                let dirName = path
                                    .dirname(file.replace(BASE_SVG_DIR, ""))
                                    .split("/")[1];
                                if (typeof map[dirName] === "undefined") {
                                    map[dirName] = [];
                                    finalFiles[dirName] = [];
                                }

                                map[dirName].push(file);
                            }
                            for (let spriteSheet in map) {
                                let files = map[spriteSheet];
                                let localConfig = Object.create(spriterConfig);
                                localConfig.dest = `${localConfig.dest}${spriteSheet}`;
                                const spriter = new SVGSpriter(localConfig);
                                for (let file of files) {
                                    spriter.add(
                                        file,
                                        path.basename(file),
                                        fs.readFileSync(file, {
                                            encoding: "utf-8",
                                        })
                                    );
                                }

                                spriter.compile((error, result) => {
                                    for (let mode in result) {
                                        for (let resource in result[mode]) {
                                            let dirName = path.dirname(
                                                result[mode][resource].path
                                            );
                                            fs.mkdirSync(dirName, {
                                                recursive: true,
                                            });
                                            fs.writeFileSync(
                                                result[mode][resource].path,
                                                result[mode][resource].contents
                                            );

                                            finalFiles[spriteSheet] =
                                                JS_ASSET_PATH +
                                                path.relative(
                                                    "public/dist/js",
                                                    result[mode][resource].path
                                                );
                                        }
                                    }
                                });
                            }
                            return `sprite_sheets: ${JSON.stringify(
                                finalFiles
                            )},`;
                        },
                    },
                },
                {
                    test: /\.vue$/,
                    use: [
                        {
                            loader: "vue-loader",
                            options: {
                                compilerOptions: {
                                    whitespace: "condense",
                                    comments: false,
                                },
                            },
                        },
                    ],
                },
                {
                    test: /\.(json5?|ya?ml)$/,
                    type: "javascript/auto",
                    include: [
                        //path.resolve(__dirname, './src/locales'),
                    ],
                    loader: "@intlify/vue-i18n-loader",
                },
                {
                    resourceQuery: /blockType=i18n/,
                    type: "javascript/auto",
                    loader: "@intlify/vue-i18n-loader",
                },
                {
                    test: /\.s?css$/,
                    use: [
                        "style-loader",
                        {
                            loader: MiniCssExtractPlugin.loader,
                            options: {
                                esModule: false,
                            },
                        },
                        "css-loader",
                        {
                            loader: "sass-loader",
                            options: {
                                additionalData: `@import "@/../../css/util/_theme_mixins.scss";`,
                            },
                        },
                    ],
                },
                {
                    test: /\.(png|jpe?g|gif|webp)(\?.*)?$/,
                    type: "asset/resource",
                    generator: {
                        filename: "images/[contenthash][ext]",
                    },
                },
            ],
        },
        plugins: [
            new webpack.DefinePlugin({
                "process.env": {
                    STORAGE_DOMAIN: JSON.stringify(
                        process.env.STORAGE_DOMAIN || "https://brkcdn.com"
                    ),
                    STORAGE_PENDING_512: JSON.stringify(
                        process.env.STORAGE_PENDING_512 ||
                            "/default/pending.png"
                    ),
                    STORAGE_PENDING_SET: JSON.stringify(
                        process.env.STORAGE_PENDING_SET ||
                            "/default/pendingset.png"
                    ),
                    STORAGE_DECLINED_512: JSON.stringify(
                        process.env.STORAGE_DECLINED_512 ||
                            "/default/declined.png"
                    ),
                    STORAGE_DECLINED_SET: JSON.stringify(
                        process.env.STORAGE_DECLINED_SET ||
                            "/default/declinedset.png"
                    ),
                    STORAGE_AVATARS_LOC: JSON.stringify(
                        process.env.STORAGE_AVATARS_LOC || "/images/avatars/"
                    ),
                    STORAGE_ITEMS_LOC: JSON.stringify(
                        process.env.STORAGE_ITEMS_LOC ||
                            "/v2/images/shop/thumbnails/"
                    ),
                    API_URL: JSON.stringify(
                        process.env.API_URL || "https://api.brick-hill.com"
                    ),
                    MAIN_ACCOUNT_ID: JSON.stringify(
                        process.env.MAIN_ACCOUNT_ID || 1003
                    ),
                    STRIPE_PUBLIC: JSON.stringify(
                        process.env.APP_ENV === "prod" ||
                            process.env.APP_ENV === "production"
                            ? process.env.STRIPE_LIVE_KEY_PUBLIC ||
                                  "Production Stripe Public key unset"
                            : process.env.STRIPE_TESTING_KEY_PUBLIC ||
                                  "Testing Stripe Public key unset"
                    ),
                },
                __VUE_OPTIONS_API__: false,
                __VUE_PROD_DEVTOOLS__: false,
                __VUE_I18N_FULL_INSTALL__: true,
                __VUE_I18N_LEGACY_API__: false,
                __INTLIFY_PROD_DEVTOOLS__: false,
            }),
            new CopyPlugin({
                patterns: [
                    {
                        from: "./resources/assets/images/extract/**",
                        to: "images/extracted/[contenthash][ext]",
                    },
                ],
            }),
            new WebpackManifestPlugin({
                basePath: "/",
                fileName: "../../mix-manifest.json",
                generate: (seed, files, entries) => {
                    let mappedFiles = Object.assign(
                        ...files.map((file) => {
                            return { [file.name]: file.path };
                        })
                    );
                    manifestFiles = { ...manifestFiles, ...mappedFiles };
                    return manifestFiles;
                },
                filter: (file) => {
                    return (
                        file.isInitial ||
                        (file.isAsset && file.name.includes("extracted"))
                    );
                },
            }),
            new CleanWebpackPlugin({
                cleanOnceBeforeBuildPatterns: ["**/*", "!sprites/**"],
            }),
            new VueLoaderPlugin(),
            new MiniCssExtractPlugin({
                filename: "[contenthash].css",
            }),
        ],
        entry: {
            register: "./resources/assets/js/vue/registerComponents.ts",
            legacy: [
                "./resources/assets/js/clans.js",
                "./resources/assets/js/main.js",
            ],
            vue: {
                import: "./resources/assets/js/vue/app.ts",
                dependOn: "register",
            },
            admin: {
                import: "./resources/assets/js/vue/adminApp.ts",
                dependOn: "register",
            },
            superadmin: {
                import: "./resources/assets/js/vue/superAdminApp.ts",
                dependOn: "register",
            },
            vendor: ["./resources/assets/js/vue/vendor.js"],
        },
        output: {
            path: path.resolve(__dirname, "public/dist/js"),
            filename: "[contenthash].js",
            chunkFilename: "vue/components/[contenthash].js",
            publicPath: JS_ASSET_PATH,
        },
        optimization: {
            runtimeChunk: "single",
            minimizer: [
                new EsbuildPlugin({
                    target: "es2015",
                    css: true,
                }),
            ],
        },
        resolve: {
            alias: {
                "vue-i18n": "vue-i18n/dist/vue-i18n.runtime.esm-bundler.js",
                vue: "vue/dist/vue.esm-bundler.js",
                "@": path.resolve("./resources/assets/js/vue"),
                components: path.resolve(
                    "./resources/assets/js/vue/components"
                ),
                media: path.resolve("./resources/assets/"),
            },
            extensions: [".ts", ".js"],
        },
    },
];
