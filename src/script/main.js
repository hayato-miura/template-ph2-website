

// 学習時間の棒グラフ

var barChartData = {
  // からの配列を作る
    labels: ['','2','','4','','6','','8','','10','','12','','14','','16','','18','','20','','22','','24','','26','','28','','30',''],
    datasets: [
      {
        label: 'blue',
        data: ['3','5','5','4','2','5','4','7','3','7','3','6','4','2','3','1','5','2','5','4','2','3','5','5','2','3','4','2','3','8','5'],
        borderColor : "rgba(54,164,235,0.8)",
        backgroundColor : "rgba(54,164,235,0.5)"
      },
    ],
  };
  
  // var complexChartOption = {
  //   responsive: true,
  // };
  
  window.onload = function() {
    ctx = document.getElementById("canvas").getContext("2d");
    window.myBar = new Chart(ctx, {
      type: 'bar',
      data: barChartData,
      // options: complexChartOption
      options :{ 
        responsive: true,
        scales: {
          xAxes: [{
            display: true,
            stacked: false,
            gridLines: {
              display: false
            }
          }],
          yAxes: [{
            gridLines: {
              display: false
            },
            ticks: {
              suggestedMin: 0,
              suggestedMax: 8,
              stepSize: 2,
              callback: function(tick) {
                return tick.toString() + 'h';
              }
            }
            

            
          }]
        }
      }
    });
  };


//   学習コンテンツドーナツグラフ
// プラグイン関数の定義
var dataLabelPlugin = {
  afterDatasetsDraw: function (chart) {
    var ctx = chart.ctx;
    chart.data.datasets.forEach(function (dataset, 系列) {
      var meta = chart.getDatasetMeta(系列);
      if (!meta.hidden) {
        meta.data.forEach(function (element, 要素) {
          // ステップ１　数値を文字列に変換
          // dataSum += dataset.data[要素];
          // var ratio = dataset.data[要素]/dataSum*100;
          // var dataString = ratio.toString();
          var dataString = dataset.data[要素].toString();
          // ステップ２　文字列の書体
          ctx.fillStyle = "#fff";            // 色　'rgb(0, 0, 0)', 'rgba(192, 80, 77, 0.7)'
          var fontSize = 14;                  // サイズ
          var fontStyle = "normal";           // 書体 "bold", "italic"
          // var fontFamily = "serif";           // フォントの種類 "sans-serif", "ＭＳ 明朝"
          ctx.font = Chart.helpers.fontString(fontSize, fontStyle);
          // ステップ３　文字列の位置の基準点
          ctx.textAlign = 'center';           // 文字列　start, end, left, right, center
          ctx.textBaseline = 'middle';        // 文字高　middle, top, bottom
          // ステップ４　文字列のグラフでの位置
          var padding = 5;                   // 点と文字列の距離
          var position = element.tooltipPosition(); //文字列の表示　 fillText(文字列, Ｘ位置, Ｙ位置)
          ctx.fillText(dataString + '%', position.x, position.y - (fontSize / 2) - padding);
        });
      }
    });
  }
};


  var ctx = document.getElementById("myDoughnutChart1");
  var myDoughnutChart1= new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ["JavaScript", "CSS", "PHP", "HTML"], 
      //データ項目のラベル
      // ラベルを消してHTMLCSSで調整する
      datasets: [{
          backgroundColor: [
              "#0345ec",
              "#0f71bd",
              "#20bdde",
              "#3ccefe"
          ],
          data: [45, 32, 18, 5] //グラフのデータ
      }]
    },
    plugins: [dataLabelPlugin],

    options: {
      title: {
        display: false,

        //グラフタイトル
      },
      legend:{
        display:false
      },
      responsive: true,
      tooltips: {
        callbacks: {
          label: function (tooltipItem, data) {
            return data.labels[tooltipItem.index]
              + ": "
              + data.datasets[0].data[tooltipItem.index]
              + " %"; //ここで単位を付けます
          }
        }
      }
      
    },
  });


  var ctx = document.getElementById("myDoughnutChart2");
  var myDoughnutChart2= new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ["ドットインストール", "N予備校", "POSSE課題"], 
      //データ項目のラベル
      // ラベルを消してHTMLCSSで調整する
      datasets: [{
          backgroundColor: [
              "#0345ec",
              "#0f71bd",
              "#20bdde",
              "#3ccefe"
          ],
          data: [30,30,40] //グラフのデータ
      }]
    },
    plugins: [dataLabelPlugin],

    options: {
      title: {
        display: false,

        //グラフタイトル
      },
      legend:{
        display:false
      },
      responsive: true,
      tooltips: {
        callbacks: {
          label: function (tooltipItem, data) {
            return data.labels[tooltipItem.index]
              + ": "
              + data.datasets[0].data[tooltipItem.index]
              + " %"; //ここで単位を付けます
          }
        }
      }
      
    },
  });


//  モーダルローディング
var button = $('.button'),
    loadComp = $('.load__complete__box'),
    upSide = $('.upside'),
    spinner = '<span class="spinner"></span>';
    form = document.querySelector('form');



button.click(function() {
  if (!button.hasClass('loading')) {
    button.toggleClass('loading').html(spinner);
    // $(function () { 
    //   setTimeout(function () { 
    //     upSide.addClass('load__complete');
    //    },0);
    //  });
    const formData = new FormData(form);
    $(function () { 
      fetch('index.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.text())
      .then(data => {
        console.log(data);
        
      });
        button.addClass('load__complete');
        loadComp.addClass('load__complete');
     });
  }
  else {
    button.toggleClass('loading').html("Load");
  }
}) ;

// twitter入力フォーム





const twitterShareBtn = document.querySelector('.twitter-share-btn'); 
const twitterShare = document.getElementById("twitter")
// twitter共有機能

twitterShare.addEventListener('click', function(event) {
  // event.preventDefault();
  var left = Math.round(window.screen.width / 2 - 275);
  var top = (window.screen.height > 420) ? Math.round(window.screen.height / 2 - 210) : 0;
  if(twitterShareBtn.checked){
    window.open(
      "https://twitter.com/intent/tweet?text=" + encodeURIComponent(document.getElementById("content").value),
      null,
      "scrollbars=yes,resizable=yes,toolbar=no,location=yes,width=550,height=420,left=" + left + ",top=" + top);
  };


});

