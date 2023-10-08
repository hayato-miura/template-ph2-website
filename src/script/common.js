

$(function(){
	// 変数に要素を入れる
	var open = $('.modal-open'),
		close = $('.modal-close'),
		container = $('.modal-container'),
		studyLang = $('.study__lang__list');
		studyContent = $('.study__content__list');
		barContainer = $('.container');
	//開くボタンをクリックしたらモーダルを表示する
	open.on('click',function(){	
		container.addClass('active');
		studyLang.addClass('active');
		studyContent.addClass('active');
		barContainer.addClass('active');
		return false;
	});

	//閉じるボタンをクリックしたらモーダルを閉じる
	close.on('click',function(){	
		container.removeClass('active');
		studyLang.removeClass('active');
		studyContent.removeClass('active');
		barContainer.removeClass('active');
	});

	//モーダルの外側をクリックしたらモーダルを閉じる
	$(document).on('click',function(e) {
		if(!$(e.target).closest('.modal-body').length) {
			container.removeClass('active');
			studyLang.removeClass('active');
			studyContent.removeClass('active');
			barContainer.removeClass('active');
		}
	});
});



