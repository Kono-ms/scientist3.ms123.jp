// Cookie Consent プラグインの取得
window.cc = initCookieConsent();
// プラグインを設定した内容で動作させる
cc.run({
    current_lang : 'en',  // 言語設定。多言語対応については後述します。
    autoclear_cookies : true,  // 特定のカテゴリのCookieがオプトアウトされたらクッキーを自動的に削除する。 default: false
    cookie_name: 'sc_cookie', // このプラグインのCookieの名前。 default: 'cc_cookie'
    cookie_expiration : 365,  // このプラグインのCookieの有効期限。 default: 182
    page_scripts: true,  // ここで紹介するようにscriptタグの属性値でプラグインを設定する場合は trueにする。  default: false
    // auto_language: null,  // 言語の自動設定。後述します。  default: null; could also be 'browser' or 'document'
    // autorun: true,  // default: true
    // delay: 0,  // default: 0
    // force_consent: false,
    // hide_from_bots: false,                   // default: false
    // remove_cookie_tables: false              // default: false
    // cookie_domain: location.hostname,        // default: current domain
    // cookie_path: "/",                        // default: root
    // cookie_same_site: "Lax",
    // use_rfc_cookie: false,                   // default: false
     revision: 1,                             // default: 0
    // レイアウトの設定。ここにあるオプションを変更することでレイアウトが変更できます。
    gui_options: {
        consent_modal: {
            layout: 'box',                      // box,cloud,bar
            position: 'bottom right',           // bottom,middle,top + left,right,center
            transition: 'slide'                 // zoom,slide
        },
        settings_modal: {
            layout: 'box',                      // box,bar
            // position: 'left',                // right,left (available only if bar layout selected)
            transition: 'slide'                 // zoom,slide
        }
    },
    // 初回動作時に実行したいコード
    onFirstAction: function(){
        console.log('onFirstAction fired');
    },
    // Cookieが同意された際に実行したいコード
    onAccept: function (cookie) {
        console.log('onAccept fired ...');
    },
    // Cookie同意内容が変更された際に実行したいコード
    onChange: function(cookie, changed_preferences) {
        if (cc.allowedCategory('performance')) {
            gtag('consent', 'update', {
                'analytics_storage': 'granted'
            });
        } else {
            gtag('consent', 'update', {
                'analytics_storage': 'denied'
            });
        }
    },
    // 言語別の設定
    languages: {
        'en': {
            // モーダルのテキスト
            consent_modal: {
            //    title: ' We use cookies! ',
                description: 'We use cookies to optimize websites display and improve our service, manage information entered by users, Statistical research and analysis of websites access status. For more information, please read our <a href="https://www.cosmobio.com/en/sustainability/governance/cookie/">Cookie Policy</a> If you consent or refuse to each category of cookies, please visit your <button type="button" data-cc="c-settings" class="cc-link">Cookie Settings</button>.',
                primary_btn: {
                    text: 'Accept All Cookies',
                    role: 'accept_all'              // 'accept_selected' or 'accept_all'
                },
                secondary_btn: {
                    text: 'Reject All Cookies',
                    role: 'accept_necessary'        // 'settings' or 'accept_necessary'
                }
            },
            // Cookieの詳細設定のテキスト
            settings_modal: {
                title: `Cookies Settings`,
                save_settings_btn: 'Save my choices （Save my settings）',
                accept_all_btn: 'Accept All Cookies',
                reject_all_btn: 'Reject All Cookies',
                close_btn_label: 'Close',
                // それぞれのクッキーの説明を記載するテーブルの項目名
                cookie_table_headers: [
                    {col1: 'Name'},
                    {col2: 'Domain'},
                    {col3: 'Expiration'},
                    {col4: 'Description'}
                ],
                blocks: [
                    {
                        title: 'Use of Cookies',
                        description: 'We use cookies to ensure basic website functionality and to provide you with a more personalized web experience. For each category, you can always choose not to allow cookies and other trackers. For more information about cookies and other sensitive data, please see our <a href="https://www.cosmobio.com/en/sustainability/governance/cookie/" class="cc-link">Cookie Policy</a> .'
                    }, {
                        title: 'Strictly necessary cookies',
                        description: 'These cookies are essential in order to enable you to move around the website and use its features, such as accessing secure areas of the website. Without these cookies services you have asked for, like shopping baskets or e-billing, cannot be provided.',
                        toggle: {
                            value: 'necessary',  // Cookieのカテゴリ名。ここで定義したカテゴリ名を、scriptタグのdata-cookiecategory属性に指定します。
                            enabled: true,
                            readonly: true          // cookie categories with readonly=true are all treated as "necessary cookies"
                        }
                    }, {
                        title: 'Performance cookies',
                        description: 'These cookies collect information about how visitors use a website, for instance which pages visitors go to most often, and if they get error messages from web pages. These cookies do not collect information that identifies a visitor. All information these cookies collect is aggregated and therefore anonymous. It is only used to improve how a website works.',
                        toggle: {
                            value: 'performance',  // Cookieのカテゴリ名。ここで定義したカテゴリ名を、scriptタグのdata-cookiecategory属性に指定します。
                            enabled: false,
                            readonly: false
                        },
                        // 上述したテーブルの項目名に合わせて、テキストを記載します。
                        // この cookie_table そのものを省略すれば、各Cookieの説明も省略できます。
                      /*  cookie_table: [
                            {
                                col1: '^_ga',
                                col2: 'google.com',
                                col3: '2 years',
                                col4: 'description ...',
                                is_regex: true  // 正規表現を有効にするかどうかの設定
                            },
                            {
                                col1: '_gid',
                                col2: 'google.com',
                                col3: '1 day',
                                col4: 'description ...',
                            }
                        ] */
                    }, 
                    
                  /*  {
                        title: '広告クッキー',
                        description: 'このクッキーは、弊社のマーケティングや広告パートナーにより設定される場合があります。このクッキーはお客様の閲覧履歴情報に基づくプロファイルを作成し、お客さまの興味・関心に応じた製品やサービスのご案内や、弊社または弊社以外のサイトでも関連性のある広告を表示するため等に使用されます。',
                        toggle: {
                            value: 'targeting',  // Cookieのカテゴリ名。ここで定義したカテゴリ名を、scriptタグのdata-cookiecategory属性に指定します。
                            enabled: false,
                            readonly: false
                        } 
                    }, */
                    
                /*    {
                        title: 'More information',
                        description: 'For any queries in relation to my policy on cookies and your choices, please <a class="cc-link" href="https://orestbida.com/contact">contact me</a>.',
                    } */
                ]
            }
        }
    }
});
