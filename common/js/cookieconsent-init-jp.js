// Cookie Consent プラグインの取得
  window.cc = initCookieConsent();
// プラグインを設定した内容で動作させる
cc.run({
    current_lang : 'jp',  // 言語設定。多言語対応については後述します。
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
        'jp': {
            // モーダルのテキスト
            consent_modal: {
            //    title: ' We use cookies! ',
                description: '当社は、ウェブサイトの最適な表示およびサービスの向上、お客様が入力された情報の管理、ウェブサイトのアクセス状況の統計的な調査・分析のためCookieを使用します。 詳細については、当社の<a href="https://scientist-cube.com/cookie/">クッキーポリシー</a>をお読みください。各カテゴリーのCookieに同意または拒否する場合は、<button type="button" data-cc="c-settings" class="cc-link">クッキーの設定</button>にアクセスしてください。',
                primary_btn: {
                    text: '全て同意する',
                    role: 'accept_all'              // 'accept_selected' or 'accept_all'
                },
                secondary_btn: {
                    text: '全て拒否する',
                    role: 'accept_necessary'        // 'settings' or 'accept_necessary'
                }
            },
            // Cookieの詳細設定のテキスト
            settings_modal: {
                title: `クッキーの設定`,
                save_settings_btn: '設定を保存する',
                accept_all_btn: '全て同意する',
                reject_all_btn: '全て拒否する',
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
                        title: 'クッキーの使用',
                        description: 'ウェブサイトの基本機能を確保し、よりパーソナライズされたウェブ体験の向上のためにCookie を使用します。各カテゴリごとに、いつでもクッキーおよびその他のトラッカーを許可しないよう選択することができます。Cookie やその他の機密データに関する詳細については、当社の<a href="https://scientist-cube.com/cookie/" class="cc-link">クッキーポリシー</a>をご覧ください。.'
                    }, {
                        title: '必須クッキー',
                        description: 'このクッキーは、お客様がWebサイトのセキュアエリアにアクセスするなどWebサイト上を移動し、その機能を利用するために必要なものです。このクッキーがなければ、お客様が希望されるショッピングカートやeビリングなどサービスを提供することができません。',
                        toggle: {
                            value: 'necessary',  // Cookieのカテゴリ名。ここで定義したカテゴリ名を、scriptタグのdata-cookiecategory属性に指定します。
                            enabled: true,
                            readonly: true          // cookie categories with readonly=true are all treated as "necessary cookies"
                        }
                    }, {
                        title: 'パフォーマンスクッキー',
                        description: 'このクッキーは、訪問者がどのページを最も頻繁に閲覧しているかや、Webページ上でエラーメッセージが表示されたかなど、訪問者がWebサイトをどのように利用しているかに関する情報を収集します。このクッキーは、訪問者を識別する情報は収集しません。このクッキーが収集する情報はすべて集計されているため、匿名化されています。これらの情報は、Webサイトの機能を改善するためにのみ利用されます。',
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
                        title: '分析クッキー',
                        description: 'このクッキーは、弊社のマーケティングや広告パートナーにより設定される場合があります。このクッキーはお客様の閲覧履歴情報に基づくプロファイルを作成し、お客さまの興味・関心に応じた製品やサービスのご案内や、弊社または弊社以外のサイトでも関連性のある広告を表示するため等に使用されます。',
                        toggle: {
                            value: 'analytics',  // Cookieのカテゴリ名。ここで定義したカテゴリ名を、scriptタグのdata-cookiecategory属性に指定します。
                            enabled: false,
                            readonly: false
                        } 
                    }, */
                    
              
                ]
            }
        }
    }
});
