/**
 * NOTE: 2025.01.08 common.jsについて
 * module, utility （共通・汎用関数）
 * form utility（フォーム関連、ただしバリデーションはhead.html,head1.html,head2.htmlへ記載）
**/



/**
 * module, utility
 * NOTE: ページ共通部品、使いまわし可能な関数など
**/

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
// NOTE: 2024.03.15
$(function(){
  $('[type="file"]').each(function(inputID){

    let $input = $(this);

    // cancel
    if($input.is(':hidden')) return true;

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
    let $name = $('[data-input-file-name-id="'+inputID+'"]');

    // event init (onload)
    inputFileHandler(true);

    // event change
    $input.on('change',function(){inputFileHandler()});

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
          fileName = $input[0].files[0].name;
        }
      }
      // rename
      $name.text(fileName);
      // change style
      $group.toggleClass('is--selected',fileName !== fileNameDefault);
    }
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

// 必須マークの調整（html調整）
$(function () {
  $('.formset__must').each(function(key, val) {
    $(this).hide();
    var html = $(this).parent().html();
    html = '<span style="color:#c80000;">*</span>' + html;
    $(this).parent().html(html);
  });
});

// .select（html調整）
$(function () {
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
});

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

