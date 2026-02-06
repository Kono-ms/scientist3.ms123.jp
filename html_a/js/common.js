$(function(){
	menuCompact();
	requiredMark();
	radioContextToggle();
	menuFixed();
	previewImage ();
});
$(window).on('resize load', function(){
	sameHeight();
	heightFix();
});


function requiredMark(){
	if(!$('.table')[0]) return;
	$('.table tr').each(function(){
		let $tr=$(this);
		if($('[required]',$tr).length>0){
			$('th:first-child',$tr).append('<span class="required-item">*</span>');
		}
	});
}


function radioContextToggle(){
	if(!$('.radio_context_toggle')[0]) return;
	$('.radio_context_toggle').each(function(){
		let $me=$(this);
		$('input[type=radio]',this).on('click',function(){
			if($(this).attr('data-show')=='true'){
				$('.radio_context',$me).show();
			}else{
				$('.radio_context',$me).hide();
			}
		});
	});
}

function menuFixed(){
	
	if(!$('.menu_fixed')[0]) return;
	
	//init
	let $menu = $('.menu_fixed');
	let offset = $menu.offset();
	if($(window).scrollTop() > offset.top) {
		
		$menu.width($menu.parent().width());
		$menu.parent().css('padding-top',$menu.height()+'px');
		$menu.addClass('active');
		
	} else {
		
		$menu.parent().removeAttr('style');
		$menu.removeAttr('style');
		$menu.removeClass('active');
	}
	
	$(window).on('scroll resize', function() {
		
		if($(window).scrollTop() > offset.top) {
			
			$menu.width($menu.parent().width());
			$menu.parent().css('padding-top',$menu.height()+'px');
			$menu.addClass('active');
			
		} else {
			
			$menu.parent().removeAttr('style');
			$menu.removeAttr('style');
			$menu.removeClass('active');
		}
	});
}

//sameHeight セットした要素たちの高さを揃える
function sameHeight(){
	
	if(!$('.same_height')[0]) return;
	
	let maxHeight=0;
	let timer = false;
	
	//style属性をリセット
	$('.same_height').removeAttr('style');

	if (timer!==false) { clearTimeout(timer); }
	
	timer=setTimeout(function() {

		if (window.matchMedia('screen and (min-width:768px)').matches) {
			
			$('.same_height').each(function() {
				
				if(maxHeight<$(this).height()){
					
					maxHeight=$(this).height();
				}
			});
			
			$('.same_height').height(maxHeight);
		}
	}, 0);
}

//heightFix セットした要素の子要素たちの高さを揃える
function heightFix(){
	
	if(!$('.height_fix')[0]) return;
	
	let maxHeight=0;
	let timer = false;
	
	//style属性をリセット
	$('.height_fix').children().removeAttr('style');
	
	if (timer!==false) { clearTimeout(timer); }
	
	timer=setTimeout(function() {

		if (window.matchMedia('screen and (min-width:768px)').matches) {

			$('.height_fix').each(function() {
				
				maxHeight=0;
				
				$(this).children().each(function() {
					
					if(maxHeight<$(this).height()){
						
						maxHeight=$(this).height();
					}
				});
				
				$(this).children().height(maxHeight);
			});
		}
	}, 0);
}

//イメージ画像プレビュー
function previewImage (){

	if(!$('.list_photo_select').find('img')[0]) return;
	
	$('.list_photo_select input[type=file]').on('change',function(){
		
		let box = $(this).closest('.list_photo_select li');
		
		let filePath = $(this).prop('files')[0];
		let fileReader = new FileReader();
		
		if(filePath.type.match('image.*')){
			
			fileReader.onload = function() {
				
				$('img',box).replaceWith(function() {
					
					let img =$('<img>');
					img.attr('src', fileReader.result);
					return img;
				});
			}
			fileReader.readAsDataURL(filePath);
		}
	});
}

//メニューの開閉を記憶する
function menuCompact(){
	
	//ページロード時の開閉アニメーションを無効にする
	$('body').addClass('hold-transition');
	
	if(localStorage.getItem('menuCompact')=='true'){

		$('body').addClass('sidebar-collapse');
		
	}else{
		
		$('body').removeClass('sidebar-collapse');
	}
	
	$('.sidebar-toggle').on('click',function(){
		
		//開閉アニメーションを有効にする
		if($('body').hasClass('hold-transition')){
		
			$('body').removeClass('hold-transition');
		}
		
		//最小化を指定されている場合
		if($('body').hasClass('sidebar-collapse')){
			
			//最大化を記憶	
			localStorage.setItem('menuCompact','false');
			
		}else{
			
			//最小化を記憶	
			localStorage.setItem('menuCompact','true');
		}
	});
}



//大カテゴリー小カテゴリープルダウン
let smallCateId = "";

$(window).load(function() {
    if ($('.bigCategoryList').length > 0) {
        //小カテゴリーの最後の要素の値
        let valSmall = $(".smallCategoryList option")[$(".smallCategoryList option").length - 1].value;
        //IDを取得
        smallCateId = valSmall.split(':')[0];
        //チェンジイベント
        $(document).on('change', '.bigCategoryList', function() {
            makeSmallCategoryList();
        });
		makeSmallCategoryList();
    }

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
});


// アコーディオン
$(function () {

	let speed = 400;// 動作の速さ
	$('[data-acc]').each(function () {
		let $group = $(this);
		let $title = $group.children('[data-acc-title]');
		let $body = $group.children('[data-acc-body]');
		let is__childAcc = $group.is($('[data-acc] [data-acc]'));

		// init
		$body.hide();
		if (is__childAcc) {
			$('input, select, textarea',$body).each(function(){
				let $input = $(this);
				let type = $input.attr('type');
				let is__select = $input.prop('tagName').toLowerCase()=='select';

				// hiddenは飛ばす
				if(type=='hidden') return true;

				// チェックのないラジオボタンなどは飛ばす
				if((type=='raido'||type=='checkbox')&&$input.prop('checked')===false) return true;

				// 初期化時に入力部品に入力がある場合は、タイトルをアクティブ化
				if($input.val()!='') {
					if(is__select) {
						if(
							$(':selected',$input).attr('value')!=''// 選択中optionの値が空　を否定
							&&$input.is(':empty')==false// selectのoptionが1つもない　を否定
						){
							$title.addClass('is--active');
						}
					}else{
						$title.addClass('is--active');
					}
				}
			});
		}
		// アクティブの指定ありで初期状態がオープン状態
		if ($title.hasClass('is--active')) $body.removeAttr('style');

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