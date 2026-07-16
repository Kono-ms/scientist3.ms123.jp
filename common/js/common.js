/**
 * NOTE: 2025.01.08 common.jsについて
 * module, utility （共通・汎用関数）
 * form utility（フォーム関連、ただしバリデーションはhead.html,head1.html,head2.htmlへ記載）
**/



/**
 * module, utility
 * NOTE: ページ共通部品、使いまわし可能な関数など
**/

// bfcache
// event.persisted が true なら、bfcache（キャッシュ）から復元された
window.addEventListener('pageshow', (event) => {
  if (event.persisted) {
    // bfcache
    $('body').removeClass('on--bfcache');// リセット
    $('body').addClass('on--bfcache');
    $(document).trigger('sc3Bfcache');

  } else {
    // 通常の新規読み込み
    $('body').removeClass('on--bfcache');
  }
});



// loading
$(function () {
  var h = $(window).height();
  $('#loading__wrapper').css('display', 'none');
  $('#is-loading ,#loading').height(h).css('display', 'block');
});
$(window).load(function () {
  $('#is-loading').delay(900).fadeOut(800);
  $('#loading').delay(600).fadeOut(300);
  $('#loading__wrapper').css('display', 'block');
});
$(function () {
  setTimeout('stopload()', 10000);
});
function stopload() {
  $('#loading__wrapper').css('display', 'block');
  $('#is-loading').delay(900).fadeOut(800);
  $('#loading').delay(600).fadeOut(300);
}


// スクロールでフェードイン
$(function () {
  $(window).on('scroll', function () {
    $('.effect-fade').each(function () {
      var elemPos = $(this).offset().top;
      var scroll = $(window).scrollTop();
      var windowHeight = $(window).height();
      if (scroll > elemPos - windowHeight) {
        $(this).addClass('effect-scroll');
      }
    });
  });
});


// アンカーリンク 高さ調整
$(function () {
  var headerHight = 90; //ヘッダの高さ
  $('a[href^=#]').on('click', function () {
    var href = $(this).attr("href");
    var target = $(href == "#" || href == "" ? 'html' : href);
    var position = target.offset().top - headerHight; //ヘッダの高さ分位置をずらす
    $("html, body").animate({ scrollTop: position }, 550, "swing");
    return false;
  });
});


// modal
// NOTE: JS版のモーダルです。ほとんどの場合base.css記載のcssのみのモーダルをサイト内では使用しています。
$(function () {
  $('.js-modal-open').on('click', function (e) {
    e.preventDefault();
    $('.js-modal').stop().fadeIn();// NOTE: ラッパーのhtmlにクラス名js-modalを指定し、CSSにてdisplay: noneを設定する必要があります。
    return false;
  });
  $('.js-modal-close').on('click', function (e) {
    e.preventDefault();
    $('.js-modal').stop().fadeOut();// NOTE: ラッパーのhtmlにクラス名js-modalを指定し、CSSにてdisplay: noneを設定する必要があります。
    return false;
  });
});


// スクロールでフェードイン
$(function () {
  $(window).on('scroll', function () {
    $('.effect-fade').each(function () {
      var elemPos = $(this).offset().top;
      var scroll = $(window).scrollTop();
      var windowHeight = $(window).height();
      if (scroll > elemPos - windowHeight) {
        $(this).addClass('effect-scroll');
      }
    });
  });
});


// ブログページ
// パンくず
$(function () {
  $('#info .breadcrumb li').each(function () {
    let $li = $(this);
    let html = $li.html();
    html = html.replace(/&gt;/g, '').replace(/&nbsp;/g, '');
    $li.html(html);
  });
});


// 詳細ページ
// 文字列からリスト化
$(function () {
  $('[data-outline-head-info]').each(function () {
    let $list = $(this);
    let keywords = $list.attr('data-outline-head-info').split(',');
    if (keywords[0]) {
      $.each(keywords, function () {
        $list.append('<li>' + this + '</li>');
      });
    }
  });
});


//大カテゴリー小カテゴリープルダウン
var smallCateId = "";

$(window).on('load', function () {
  if ($('.bigCategoryList').length > 0) {
    //小カテゴリーの最後の要素の値
    let valSmall = $(".smallCategoryList option")[$(".smallCategoryList option").length - 1].value;
    //IDを取得
    smallCateId = valSmall.split(':')[0];

    function makeSmallCategoryList() {

      let smallVal = $(".smallCategoryList").val();

      //小カテゴリーのオプションを全て削除
      $(".smallCategoryList").children().remove();

      let val = $(".bigCategoryList").val();
      if (val != "") {
        val = val.split(':')[1];
      }

      let categorytags = $(".categorytags"); //カテゴリーリスト
      let find = 0;
      for (let i = 0; i < categorytags.length; i++) {
        //大カテゴリーが一致する内容を探す
        if (val == $(categorytags[i]).attr("name")) {

          let smalls = $(categorytags[i]).val().split('::');
          $(".smallCategoryList").append('<option value="">▼選択して下さい</option>');
          for (let j = 0; j < smalls.length; j++) {
            let value = smalls[j];
            let optval = smallCateId + ":" + value;
            $(".smallCategoryList").append('<option value="' + optval + '">' + value + '</option>');
          }
          find = 1;
          break;
        }
      }

      //対象カテゴリーを選択していない場合は、全項目
      if (find == 0) {
        $(".smallCategoryList").append('<option value="">▼選択して下さい</option>');
        for (let i = 0; i < categorytags.length; i++) {
          let smalls = $(categorytags[i]).val().split('::');
          for (let j = 0; j < smalls.length; j++) {
            let value = smalls[j];
            let optval = smallCateId + ":" + value;
            $(".smallCategoryList").append('<option value="' + optval + '">' + value + '</option>');
          }
        }
      }


      //再度選択
      if (smallVal != "") {

        $(".smallCategoryList").val(smallVal);
      }
    }
    //チェンジイベント
    $(document).on('change', '.bigCategoryList', function () {
      makeSmallCategoryList();
    });
    makeSmallCategoryList();
  }

});


// input file
// NOTE: 2026.05.21
// NOTE: 2026.03.18
// NOTE: 2024.03.15
$(function(){
  $('[type="file"]').each(function(inputID){

    let $input = $(this);

    // cancel
    //if($input.is(':hidden')) return true;

    // label
    $input.wrap('<label class="input-file" data-input-file-id="'+inputID+'"></label>');
    let $label = $('[data-input-file-id="'+inputID+'"]');

    // message
    let btnText = 'Select a file';
    $label.append('<span>'+btnText+'</span>');

    // group (label wrapper)
    $label.wrap('<div class="input-file-group" data-input-file-group-id="'+inputID+'"></div>');
    let $group = $('[data-input-file-group-id="'+inputID+'"]');

    // file name
    let fileNameDefault = 'no file selected';
    $label.after('<div class="input-file-name" data-input-file-name-id="'+inputID+'">'+fileNameDefault+'</div>');

    // delete
    // NOTE: without font awesome 7
    let $delete = $('<button type="button" class="input-file-delete" data-input-file-delete-id="'+inputID+'"><i class="fa-solid fa-file-circle-xmark"></i></button>');
    $label.after($delete);

    // name
    let $name = $('[data-input-file-name-id="'+inputID+'"]');

    // event init (onload)
    inputFileHandler(true);

    // event change
    $input.on('change',function(){inputFileHandler()});
    // TODO: ラッパーからinput fileに辿る必要がある
    $delete.on('click',function(){$input.val('').trigger('change')});

    function inputFileHandler(is__init){
      let fileName = fileNameDefault;
      let is__saved = Boolean($input.attr('value'));
      let is__sgif = String($input.attr('value')).indexOf('s.gif')!=-1;
      let is__selected = Boolean($input.val()) || is__saved;
      //console.log('is__selected', is__selected, 'is__init', is__init, 'is__saved', is__saved, 'is__sgif', is__sgif);
      if(is__selected){
        if(is__init){
          if(is__saved){
            if(is__sgif){
              fileName = fileNameDefault;
            }
            if(!is__sgif){
              fileName = String($input.attr('value')).split('/').pop();
            }
          }
        }else{
          fileName = $input[0]?.files[0]?.name || fileNameDefault;
        }
      }
      // rename
      $name.text(fileName);
      // change style
      $group.toggleClass('is--selected',fileName !== fileNameDefault);
      // reset hidden
      resetHiddenFileBuffer($input,$group,fileName === fileNameDefault);
    }
    function resetHiddenFileBuffer($input,$group,is__reset){
      // NOTE: 実行時に$groupが生成されていない場合があるので実行タイミングに注意が必要

      // on input
      if(!is__reset) return;

      // on delete
      const $hidden1 = $('[type="hidden"]',$group);
      const $hidden2 = $group.siblings('[type="hidden"]');
      const name = $input.attr('name');
      $hidden1.add($hidden2).each(function() {
        const $hidden = $(this);
        const key = $hidden.attr('name');
        if(name.indexOf(key)!=-1){
          // matches
          $hidden.val('');
        }
      });
    }
  });
});
$(document).on('sc3Bfcache',function(){
  $('[type="file"]').each(function(inputID){
    let $input = $(this);
    let $group = $input.closest('[data-input-file-group-id]');
    let $name = $('[data-input-file-name-id]',$group);
    let fileNameDefault = 'no file selected';
    // rename
    $name.text(fileNameDefault);
    // change style
    $group.removeClass('is--selected');
  });
});



// pdf preview (by browser)
// NOTE: 2024.12.23
$(function(){
  $('[data-outline-downloads] a').each(function () {
    let $a = $(this);
    let $group = $a.closest('[data-outline-downloads]');
    let $modal = $group.next('[data-outline-downloads-modal]');
    let $img = $('img',$a);
    let href = $a.attr('href');
    let is__img = href.match(/.+\.(png|jpg|jpeg|gif|svg|webp)$/g);
    let is__pdf = href.match(/.+\.(pdf)$/g);
    let is__365 = href.match(/.+\.(doc|docx|xls|xlsx|ppt|pptx)$/g);
    let is__noimage = !is__img && !is__pdf && !is__365;
    if (is__pdf) referenceImgField__getPDFThumbnail(href, null, $img);
    if (is__img) $img.attr('src', href);

    $a.on('click',function(e){
      e.preventDefault();
      if($('[data-outline-downloads-modal] .modal_switch').prop('checked')==false){
        let $a = $(this);
        let href = $a.attr('href');
        let is__img = href.match(/.+\.(png|jpg|jpeg|gif|svg|webp)$/g);
        let is__pdf = href.match(/.+\.(pdf)$/g);
        let is__365 = href.match(/.+\.(doc|docx|xls|xlsx|ppt|pptx)$/g);
        let is__noimage = !is__img && !is__pdf && !is__365;

        // set modal mode
        let className = null;
        if(is__img && !is__pdf && !is__365){
          className = 'is--img';
        }else if(!is__img && is__pdf && !is__365){
          className = 'is--pdf';
        }else if(!is__img && !is__pdf && is__365){
          className = 'is--365';
          return false;// don't modal open
        }else{
          className = 'is--no-image';
          return false;// don't modal open
        }
        // modal open
        $('[data-outline-downloads-modal] .modal_switch').prop('checked',true);

        // image change
        $modal.removeClass('is--img is--pdf is--365 is--no-image');
        if(className) $modal.addClass(className);
        let $pdf = $('[data-outline-downloads-preview="pdf"]',$modal);
        let $img = $('[data-outline-downloads-preview="img"]',$modal);
        // remove before item
        $pdf.attr('src', '/common/images/s.gif');
        $img.attr('src', '/common/images/s.gif');
        // reset item
        if (is__pdf) $pdf.attr('src', href);
        if (is__img) $img.attr('src', href);
      }
    });
  });
});

function referenceImgField__loadingOfThumbnail($img, off) {
  if (!off) $img.attr('src', '/common/images/img_loading.gif');
  if (off) $img.attr('src', '/common/images/s.gif');
}

function referenceImgField__getPDFThumbnail(filepath, reader, $img) {
  // NOTE: 引き数readerは使ってません。

  // loading start
  referenceImgField__loadingOfThumbnail($img);

  // PDF サムネ取得
  $.ajax({
    url: 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.6.347/pdf.min.js',
    async: true,
    cache: true,
    dataType: 'script',
  }).done(function () {

    // new PDF.js
    let pdfjsLib = window['pdfjs-dist/build/pdf'];
    // PDF.js worker path
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.6.347/pdf.worker.js';
    // サムネイルの生成
    let loadingTask = pdfjsLib.getDocument({
      url: filepath,// NOTE: URLでPDFを読み込む場合
      cMapUrl: 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.6.347/cmaps/',// NOTE: 日本語文字化け対応
      cMapPacked: true,// NOTE: 日本語文字化け対応
    });

    loadingTask.promise.then(
      function (pdf) {
        console.log('PDF loaded');
        // Fetch the first page
        pdf.getPage(1).then(function (page) {
          console.log('Page loaded');
          let scale = 1.5;
          let viewport = page.getViewport({ scale: scale });
          let canvas = document.createElement('canvas');
          let context = canvas.getContext('2d');
          canvas.height = viewport.height;
          canvas.width = viewport.width;
          // Render PDF page into canvas context
          let renderContext = {
            canvasContext: context,
            viewport: viewport,
          };
          let renderTask = page.render(renderContext);
          renderTask.promise.then(function () {
            let image = canvas.toDataURL('image/png');
            console.log('Page rendered', viewport);
            $img.attr('src', image);
          });
        });
      },
      function (reason) {
        // PDF loading error
        console.error(reason);
        alert('PDFの読み込みに失敗しました');
      }
    );
  }).fail(function (jqXHR, textStatus, errorThrown) {
    // エラーの場合処理
    alert('PDFの読み込みに失敗しました');
    referenceImgField__loadingOfThumbnail($img, true);
  });
  
}



// アコーディオン
$(function () {

  let speed = 400;// 動作の速さ
  $('[data-acc]').each(function () {
    let $group = $(this);
    let $title = $('[data-acc-title]', $group);
    let $body = $('[data-acc-body]', $group);

    // init
    $body.hide();
    if ($title.hasClass('is--active')) $body.removeAttr('style');// アクティブの指定ありで初期状態がオープン状態

    // event
    $title.on('click', function () {
      acc__checkAndChange($(this), $body);
    });
  });

  // 開閉チェックと開閉動作
  function acc__checkAndChange($title, $body) {
    let is__open = $title.hasClass('is--active');
    if (!is__open) {
      $body.stop().slideDown(speed);
      $title.addClass('is--active');
    } else {
      $body.stop().slideUp(speed);
      $title.removeClass('is--active');
    }
  }
});

// 審査依頼
function shinsa_irai(param) {
  //if (confirm('We will move on to the main examination. May I？')) {
  if (confirm('We will now move on to the main-registration review. Is that okay?')) {
    location.href = "/m_m1/?mode=shinsa&param=" + param;
  }
}

// read more
$(function () {
  window.readMoreItem = [];
  $('[data-read-more="list"]').each(function(i) {
    window.readMoreItem[i] = {defaultHeight: 0}
    let $list = $(this);
    if($list.children().length<=2) return true;
    $list.addClass('is--ready');
    $list.append('<button type="button" class="input-add input-read-more" data-read-more-button>さらに表示</button>');
    window.readMoreItem[i].defaultHeight = $list.outerHeight();
    let minHeight = $list.children().eq(0).outerHeight() + $('[data-read-more-button]',$list).outerHeight();// li height + button height
    $list.css({
      height: minHeight,
      overflow: 'hidden',
    });
    $('[data-read-more-button]',$list).on('click',function(){
      $list.animate({
        height: window.readMoreItem[i].defaultHeight
      },400,function(){
        $list.removeAttr('style');
        $list.removeClass('is--ready');
      })
    });
  });
});


// aboutページ
$(function () {
  try{
    $(".about__slider").slick({
      infinite: true,
      slidesToShow: 3,
      dots: true,
      responsive: [
        {
          breakpoint: 767,
          settings: {
            slidesToShow: 1,
          }
        },
      ]
    });
  }catch(e){console.log(e.message)}
});


/**
 * form utility
 * NOTE: フォーム関連は以下に記載
**/

// フォームの項目名の調整（html調整）
$(function () {
  editFormRequiredMark();
});
function editFormRequiredMark(){

  // NOTE: 以下の処置をすると、マークや文字、リンクなどの順番の制御が base.cssより可能になります。

  // 1. 項目名クラス .formset__ttl内部のテキストコンテンツを全てhtmlで囲う（html調整）
  $('.formset__ttl').each(function(i){
    const $ttl = $(this);
    $ttl.contents().filter(function() {
      // NOTE: nodeType === 3 はテキストノード
      return this.nodeType === 3 && $.trim(this.textContent).length > 0;
    }).wrap('<label></label>');
  });

  // 2. 必須マークの調整（html調整）
  $('.formset__must').each(function(key, val) {
    const $mark = $(this);
    $mark.addClass('formset__must--simple');
    $mark.html('<i>*</i>');
  });
}

// マイナス値の入力を自動で拒否
$(function () {
  $('[data-jquery-validate-rule="positive"').on('change',function() {
    let $input = $(this)
    let text = $input.val();
    if(!isNaN(text)){
      // NOTE: 入力値が数字なら、数値として取り扱う（それ以外の処理はバリデーションに任せる）
      if(Number(text)<1) $input.val(1);
    }
  });
});

// 小数点第3位以下を自動で拒否（フォーム系のタグ以外でも指定可能）
function dataChangeDecimalOnInput($input,decimalPlaces){
  console.log('dataChangeDecimalOnInput common.js');
  decimalPlaces = isNaN(Number(decimalPlaces)) ? 2 : Number(decimalPlaces);// デフォルトは第3位以下を拒否（＝2をセット）
  let is__formTag = ['input','select','textarea'].indexOf($input.prop('tagName').toLocaleLowerCase())!=-1;
  let value = is__formTag ? $input.val() :  $input.text();
  // 1. 数字と小数点以外を除去
  value = value.replace(/[^0-9.]/g, '');
  // 2. 小数点が2つ以上入力されないように制御
  const parts = value.split('.');
  if (parts.length > 2) {
    value = parts[0] + '.' + parts.slice(1).join('');
  }
  // 3. 小数点第2位までに制限
  if (parts.length > 1 && parts[1].length > decimalPlaces) {
    value = roundToN(Number(parts[0]+'.'+parts[1]), decimalPlaces);
  }
  // 4. タグのタイプに合わせて動作を変更
  console.log('dataChangeDecimalOnInput common.js',is__formTag,value);
  if(is__formTag) $input.val(value);
  if(!is__formTag) $input.text(value);
}
function dataChangeDecimalOnBlur($input){
  console.log('dataChangeDecimalOnBlur common.js');
  let is__formTag = ['input','select','textarea'].indexOf($input.prop('tagName').toLocaleLowerCase())!=-1;
  let value = $input.val();
  if (value !== '' && !isNaN(value)) {
    // 1.00や 1. を 1へ変換
    value = parseFloat(value).toString();
    // タグのタイプに合わせて動作を変更
    if(is__formTag) $input.val(value);
    if(!is__formTag) $input.text(value);
  }
}


// .select（html調整）
$(function () {
  setRichSelectTag();
});
function setRichSelectTag(){
  $('.formset__input select').each(function() {
  let $select = $(this);
  if($select.closest('.select').length) return true;// .formset__input .select selectは飛ばす
  // 次要素を追加し、クローンを入れる
  let $selectClone = $select.clone(true);
  $selectClone.attr('class');// bootstrapなどの影響を防ぐため、クラス名は消す
  $select.after('<span class="select"></span>');
  $select.next().append($selectClone);// イベントもコピー
  // 不要な ▼文字を消す
  $('option',$selectClone).each(function() {
    let $option = $(this);
    let text = $option.text();
    $option.text(text.replace('▼',''));
  });
  // オリジナルを消す
  $select.remove();
});
}

// パスワード表示
$(function () {
  $('[data-show-pw]').on('click',function() {
    let $btn = $(this);

    // NOTE: data-show-pw属性には、切り替えるinputの#から始まるID名が必要です。 data-show-pw="#password2"など

    if($($btn.attr('data-show-pw')).length){
      // textとpasswordの切り替え
      if($($btn.attr('data-show-pw')).attr('type')=='text') {
        $($btn.attr('data-show-pw')).attr('type','password');
        $btn.removeAttr('class');
        $btn.addClass('fa fa-eye');// NOTE: font awesomeが必要です
        console.log('data-show-pw password');
      } else if ($($btn.attr('data-show-pw')).attr('type')=='password') {
        $($btn.attr('data-show-pw')).attr('type','text');
        $btn.removeAttr('class');
        $btn.addClass('fa fa-eye-slash');// NOTE: font awesomeが必要です
        console.log('data-show-pw text');
      }
    }
  });
});

// 文字数カウント
$(function () {
  if(!$('[data-count-length]')[0]) return;
  // init
  $('[data-count-length]').each(function(){
    countLength($(this));
  });
  // on keyup (live)
  $(document).on('keyup','[data-count-length]',function () {
    countLength($(this));
  });
  // main
  function countLength($input) {

    let max = $input.attr('maxlength');
    let val = String($input.val());
    let id = $input.attr('name')+'_length';
    let len = val.length;

    //let txt = len + '文字/' + max + '文字中';
    let txt = len + '/' + max;

    let $notes = $('[id="'+id+'"]');

    // error
    $notes.removeClass('jquery-validate--error');
    if(len==Number(max)&&len!==0) $notes.addClass('jquery-validate--error');

    // text
    $notes.text(txt);
  }
});

// ローケルな金額の文字列の取得と出力
function getLocalPriceFormat($element,currency,value) {
  const isQty = false;
  // 数値変換用に標準化（EURのカンマをドットに置換）
  let cleanVal = String(value).replace(/,/g, '.');
  let num = parseFloat(cleanVal);
  if (isNaN(num)) return;
  // ロケール判定
  const locale = {
    'JPY': 'ja-JP',
    'USD': 'en-US',
    'GBP': 'en-GB',
    'EUR': 'de-DE' // ドイツ形式：1.234,56
  }[currency] || 'en-US';
  // フォーマット設定
  const options = {
    minimumFractionDigits: (currency === 'JPY' || isQty) ? 0 : 2,
    maximumFractionDigits: (currency === 'JPY' || isQty) ? 0 : 2
  };
  $element.text(new Intl.NumberFormat(locale, options).format(num));
}

// 確認画面の金額フォーマットを自動で調整
$(function () {
  setLocalPriceFormatAutomatically();
});
function setLocalPriceFormatAutomatically(){

  const currency = $('[data-currency]:eq(0)').text();
  $('[data-text-format="price"]').each(function() {
    let $price = $(this);
    getLocalPriceFormat($price,currency,$price.text());
  });
}

// 数字フォーマットを自動で適用（通貨単位も自動判別）
$(function () {
  setNumberFormat();
});
function setNumberFormat(){

  const currencyKeys = [
    '円',
    'ドル',
    'ポンド',
    'ユーロ',
    'JPY',
    'USD',
    'GBP',
    'EUR'
  ];

  $('[data-format-number]').each(function(i) {
    const $price = $(this);
    const priceText = $price.text();
    if(!priceText) return true;// 空の場合、ループをスキップ
    //console.log('setNumberFormat [data-format-number] '+i+' : ',priceText);
    let currency = $price?.attr('data-format-number') ?? false;
    if( currency !== false ){
      // 金額単位の指定あり
      // NOTE: 常に金額の数値のみ（コンマ区切り数字やただの数字）が入ってくる想定なので、そのまま getLocalPriceFormatする
      currency = currencyKeys.indexOf(currency) !=-1 ? currency : 'JPY';// currencyKeysにない金額単位の場合はJPY
      getLocalPriceFormat($price,currency,priceText);
    }else{
      // 金額単位の指定なし
      // NOTE: ダーティな金額数字が入ってくる想定（1000円、1,000円、1000、￥1000など）で数値と単位を分ける必要がある
      const regex = /^[0-9,\.]+$/;
      if(regex.test(priceText)){
        // 数値のみ
        // NOTE: ただし常にJPYとして取り扱う（単位の指定が元々ないから、その数値がどんな数値なのか不明なため一般的なフォーマットを当てる）
        getLocalPriceFormat($price,'JPY',priceText);
      }else{
        const m = priceText.match(/([0-9,\.]+)/);// 数値のテキストを抜き出す
        const findPriceText = 1 in m ? m[1] : null;
        if(!findPriceText) return true;// 空の場合、ループをスキップ
        for(let key in currencyKeys){
          if(priceText.indexOf(key) !=-1){
            currency = currencyKeys[key];
            break;
          }
        }
        if( currency !== false ){
          // 単位を加味した数値をセットする
          // TODO: 元の単位が消えてしまう
          getLocalPriceFormat($price,currency,findPriceText);
        }else{
          // 単位もない、ただの数字の場合、フォーマットするだけ
          getLocalPriceFormat($price,'JPY',findPriceText);
        }
      }
    }
  });
}


// [data-set-next-month-today]に一か月後の今日をセット
$(function () {
  if(!$('[data-set-next-month-today]')[0]) return;
  const date = new Date();
  date.setMonth(date.getMonth() + 1);
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0'); // 月は0始まりなので+1
  const day = String(date.getDate()).padStart(2, '0');  
  const targetDate = `${year}-${month}-${day}`;
  setTimeout(function(){
    $('[data-set-next-month-today]').each(function(){
      let $date = $(this);
      $date.val(targetDate).trigger('change');
      console.log('[data-set-next-month-today]', targetDate);
    });
  },10);
});

// 小数点の四捨五入
// console.log(roundToN(1.356, 2)); // 1.36
function roundToN(val, precision){
  const digit = Math.pow(10, precision);
  return Math.round(val * digit) / digit;
};
