<?php
/*
Template Name: 奄美トップ
*/
?><!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>奄美群島の地図・説明</title>
  <style>
    .amami-top-buttons {
      display: flex;
      justify-content: center;
      gap: 32px;
      margin-bottom: 32px;
    }
    .amami-top-buttons a {
      display: block;
      padding: 18px 36px;
      background: linear-gradient(45deg, #4facfe, #00f2fe);
      color: #fff;
      font-size: 1.2em;
      font-weight: bold;
      border-radius: 12px;
      text-decoration: none;
      box-shadow: 0 4px 12px rgba(0,0,0,0.12);
      transition: background 0.3s;
    }
    .amami-top-buttons a:hover {
      background: linear-gradient(45deg, #ff9800, #f093fb);
    }
    body {
      background: #f0f4f8;
      margin: 0;
      font-family: Yu Gothic, Meiryo, sans-serif;
    }
    .amami-top-main {
      max-width: 900px;
      margin: 40px auto;
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.12);
      padding: 32px 24px 48px 24px;
    }
    .amami-map-section {
      background: #f8fafc;
      border-radius: 12px;
      padding: 24px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .amami-map-section h2 {
      font-size: 1.4em;
      color: #0d235a;
      margin-bottom: 16px;
    }
  </style>
</head>
<body>
  <!-- スライドショー枠（目立つ装飾） -->
  <div style="width:100%;background:linear-gradient(90deg,#4facfe,#00f2fe);padding:36px 0 36px 0;text-align:center;box-shadow:0 8px 32px rgba(0,0,0,0.18);margin-bottom:36px;">
    <div style="display:inline-block;background:#fff;border-radius:18px;padding:24px 48px;box-shadow:0 4px 18px rgba(0,0,0,0.10);font-size:2.1em;font-weight:900;color:#0d235a;letter-spacing:0.04em;">
      東京奄美会ＨＰへようこそ    </div>
  </div>
  <!-- 下部全幅エリア -->
  <div style="width:100vw;max-width:100%;background:linear-gradient(90deg,#f093fb 0%,#f5576c 100%);padding:48px 0 56px 0;box-shadow:0 8px 32px rgba(0,0,0,0.10);margin:0;">
    <div style="max-width:1200px;margin:0 auto;">
      <!-- 3つのボタン -->
      <div style="display:flex;justify-content:center;gap:48px;margin-bottom:48px;">
        <a href="/public-5-history/" id="btn-home" style="display:block;padding:10px 36px;background:#4facfe;color:#fff;font-size:1.1em;font-weight:900;border-radius:12px;text-decoration:none;box-shadow:0 2px 8px rgba(0,0,0,0.13);transition:background 0.3s;white-space:nowrap;">東京奄美会ホームページ</a>
        <!-- <a href="https://www.amami.com/kanko/" target="_blank" rel="noopener" style="display:block;padding:10px 36px;background:#4facfe;color:#fff;font-size:1.1em;font-weight:900;border-radius:12px;text-decoration:none;box-shadow:0 2px 8px rgba(0,0,0,0.13);transition:background 0.3s;white-space:nowrap;">奄美群島観光名所</a> -->
        <a href="https://violetfoal2.sakura.ne.jp/hp-amami-pr-1/amami.html" target="_blank" rel="noopener" style="display:block;padding:10px 36px;background:#4facfe;color:#fff;font-size:1.1em;font-weight:900;border-radius:12px;text-decoration:none;box-shadow:0 2px 8px rgba(0,0,0,0.13);transition:background 0.3s;white-space:nowrap;">奄美群島観光案内</a>
        <select id="sns-link-select" style="padding:10px 36px; font-size:1.1em; border-radius:12px; min-width:220px; background:#4facfe; color:#fff; font-weight:900; box-shadow:0 2px 8px rgba(0,0,0,0.13); white-space:nowrap; border:none;" onchange="const url2=this.value;if(url2)window.open(url2,'_blank');">
          <option value="">Ｓ　Ｎ　Ｓ</option>
          <option value="https://www.amami.com/blog/">ブログ</option>
          <option value="https://www.facebook.com/amami.tokyo/">Facebook</option>
        </select>
        <select id="external-link-select" style="padding:10px 36px; font-size:1.1em; border-radius:12px; min-width:220px; background:#4facfe; color:#fff; font-weight:900; box-shadow:0 2px 8px rgba(0,0,0,0.13); white-space:nowrap; border:none;" onchange="const url1=this.value;if(url1)window.open(url1,'_blank');">
          <option value="">奄美関連サイト</option>
          <option value="https://violetfoal2.sakura.ne.jp/hp-amami-pr-1/amami.html">奄美PRサイト</option>
        </select>
      </div>
      <!-- 地図・説明 -->
      <div style="background:#fff;border-radius:18px;padding:36px 32px;box-shadow:0 2px 12px rgba(0,0,0,0.10);text-align:center;">
        <h2 style="font-size:2em;color:#0d235a;margin-bottom:24px;">奄美群島の地図・説明</h2>
        <img src="https://www.amami.com/img/amami-map.png" alt="奄美群島地図" style="width:100%;max-width:700px;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,0.10);margin-bottom:24px;">
        <p style="font-size:1.2em;line-height:2;color:#333;max-width:800px;margin:0 auto;">奄美群島は鹿児島県南部に位置し、豊かな自然と独自の文化が息づく美しい島々です。観光・歴史・グルメなど多彩な魅力を持ち、東京奄美会はその情報発信・交流の拠点です。</p>
      </div>
    </div>
  </div>
</body>
</html>
