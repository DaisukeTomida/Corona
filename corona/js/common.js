$(function(){
	$('#toggle').click(function(){
		$('#menu').slideToggle();
		return false;
	});
	$('#menuclose').click(function(){
		$('#menu').slideToggle(false);
		return false;
	});
	$(window).click(function(){
		$('.edit-menu').css('display', 'none');
	});
	$('.edit-button').click(function(e){
		var y = e.clientY;
		var x = e.clientX;
		var wh = $(window).height();
		var mh = $('.edit-menu').height();
		var fh = $('#footer').height();
		if((wh - fh)<=(y+mh)){
			y=y-mh;
		}
		row=$(this).attr('alt');
		$('.edit-menu').css('display', 'none');
		$('#edit-menu'+row).slideToggle(true);
		$('#edit-menu'+row).css('top', y+'px');
		$('#edit-menu'+row).css('left', x+'px');
		return false;
	});
	$(window).on('load resize',function(){
		var W01 = $(window).width();
		if(W01 <= '700'){;
			$('#menu').hide();
			$('#toggle').show();
			$('#menuclose').show();
			$('#title').css('left', 0);
			$('#titlename').css('float', 'left');
			$('#container').css('margin-left', 0);
			$('#footer').css('left', 0);
		}else{
			$('#menu').show();
			$('#toggle').hide();
			$('#menuclose').hide();
			$('#title').css('left', 170);
			$('#container').css('margin-left', 170);
			$('#footer').css('left', 170);
		}
	});
});
/******************************
	【チェック処理】
	パラメータ
		atai		: チェックする値
		hyouji		: メッセージで表示する値
		hissu		: 必須項目(his:必須 nin:任意)
		keta		: 桁数（バイト数）
		checktype	: 桁数チェックのタイプ（non：なし char:文字数 byte:バイト数）
		check		: チェック（non：なし num:数字のみ alpha:半角英数字 all：全角 date：日付 reg：正規表現）
		seiki		: 正規表現（checkがregの場合使用する）
	戻り値
		msg			: エラー時のメッセージ
*******************************/
var hissuname = {'nin':'任意', 'his':'必須'};
var checktypename = {'non':'なし', 'char':'文字', 'byte':'バイト'};
var checkname = {'non':'なし', 'num':'数字のみ', 'alpha':'半角英数字', 'all':'全角', 'reg':'正規表現'};
function runCheck(atai, hyouji, hissu, keta, checktype, check, seiki){
	var msg="";
	console.log('*****'+hyouji+'のチェック 値['+atai+'] 必須['+hissuname[hissu]+'] 桁数['+keta+'] タイプ['+checktypename[checktype]+'] チェック['+checkname[check]+']['+seiki+']*****');
	try{
		//未入力チェック処理
		if(msg==""){
			//必須項目で値が入っていない場合
			if(hissu=='his'&&atai.length==0){
				msg="必須項目です";
			}
		}
		//最大値のチェック処理
		if(msg==""){
			var varlen=0;
			if(checktype=='char'){
				//文字数
				varlen=atai.length;
			}else if(checktype=='byte'){
				//バイト数
				varlen=strLength(atai);
			}
			console.log('桁数：'+varlen+checktypename[checktype]);
			if(varlen!=0){
				//文字数のチェック
				if(keta<varlen){
					msg="最大値を超えています（"+(parseInt(varlen)-parseInt(keta))+checktypename[checktype]+"オーバー）";
				}
			}
		}
		//文字のチェック処理
		if(msg==""){
			if(atai.length==0){
				//文字が入力されていない場合はチェック対象外
				seiki="";
			}else{
				if(check=='non'){
					//なしの場合
					seiki="";
				}else if(check=='num'){
					//数字のみのチェック
					seiki=/[0-9]/;
				}else if(check=='alpha'){
					//半角英数字のチェック
					seiki=/[0-9A-Za-z]/;
				}else if(check=='all'){
					//全角のチェック
					if(parseInt(atai.length)*2!=parseInt(strLength(atai))){
						msg="入力が正しくありません";
					}
					seiki="";
				}else if(check=='date'){
					//日付のチェック
					if(ckDate(atai)==false){
						msg="入力が正しくありません";
					}
					seiki="";
				}else if(check=='reg'){
					//正規表現のチェック
				}
			}
			//正規表現のチェック
			if(seiki!=""){
				console.log('正規表現：'+seiki+' 結果：'+atai.match(seiki));
				if(!atai.match(seiki)){
					msg="入力が正しくありません";
				}
			}
		}
	}catch(e){
		msg=e;
	}
	console.log('結果：'+msg);
	return msg;
}
var strLength=function(str){
	var r=0; 
	try{
		for(var i=0; i<str.length; i++){
			var c=str.charCodeAt(i);
			// Shift_JIS: 0x0 ～ 0x80, 0xa0 , 0xa1 ～ 0xdf , 0xfd ～ 0xff 
			// Unicode : 0x0 ～ 0x80, 0xf8f0, 0xff61 ～ 0xff9f, 0xf8f1 ～ 0xf8f3 
			if ((c>=0x0 && c<0x81) || (c==0xf8f0) || (c>=0xff61 && c<0xffa0) || (c>=0xf8f1 && c<0xf8f4)){
				r+=1;
			}else{
				r+=2;
			}
		}
	}catch(ex){
		throw ex;
	}
	return r;
};
/****************************************************************
* 機　能： 入力された値が日付でYYYY-MM-DD形式になっているか調べる
* 引　数： datestr　入力された値
* 戻り値： 正：true　不正：false
****************************************************************/
function ckDate(datestr){
	// 正規表現による書式チェック
	if(!datestr.match(/^\d{4}\-\d{2}\-\d{2}$/)){
		return false;
	}
	var vYear = datestr.substr(0, 4) - 0;
	var vMonth = datestr.substr(5, 2) - 1; // Javascriptは、0-11で表現
	var vDay = datestr.substr(8, 2) - 0;
	// 月,日の妥当性チェック
	if(vMonth >= 0 && vMonth <= 11 && vDay >= 1 && vDay <= 31){
		var vDt = new Date(vYear, vMonth, vDay);
		if(isNaN(vDt)){
			return false;
		}else if(vDt.getFullYear() == vYear && vDt.getMonth() == vMonth && vDt.getDate() == vDay){
			return true;
		}else{
			return false;
		}
	}else{
		return false;
	}
}