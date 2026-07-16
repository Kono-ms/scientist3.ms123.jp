
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
