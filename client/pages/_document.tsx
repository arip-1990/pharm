import Document, { Html, Head, Main, NextScript } from "next/document";
import React from "react";

class MyDocument extends Document {
  render() {
    return (
      <Html lang="ru">
        <Head>
          <meta name="apple-itunes-app" content="app-id=6443518664" />
          <meta name="smartbanner:title" content="АПТЕКА 120/80" />
          <meta name="smartbanner:author" content="ООО Социальная аптека" />
          <meta name="smartbanner:price" content="бесплатно" />
          <meta
            name="smartbanner:price-suffix-google"
            content="ru.apteka120.android"
          />
          <meta
            name="smartbanner:icon-apple"
            content="https://play-lh.googleusercontent.com/DDqqkbF38Uhzr_ooOgoCLWS8K4sgoPXb2S5qkyylq8tL0P3MhDkkmYgPW4hSxpSFwoWe=w240-h480"
          />
          <meta
            name="smartbanner:icon-google"
            content="https://play-lh.googleusercontent.com/DDqqkbF38Uhzr_ooOgoCLWS8K4sgoPXb2S5qkyylq8tL0P3MhDkkmYgPW4hSxpSFwoWe=w240-h480"
          />
          <meta name="smartbanner:button" content="скачать" />
          <meta
            name="smartbanner:button-url-apple"
            content="https://apps.apple.com/app/id6443518664"
          />
          <meta
            name="smartbanner:button-url-google"
            content="https://play.google.com/store/apps/details?id=ru.apteka120.android"
          />
          <meta name="smartbanner:enabled-platforms" content="android, ios" />
          <meta name="smartbanner:line1" content="Социальная аптека" />
          <meta name="smartbanner:line2" content="[r:4.7] (23) звезды" />
          <meta name="smartbanner:line3" content="АПТЕКА 120/80" />
          <meta name="smartbanner:stars-color" content="#ff0000" />
          <link
            rel="stylesheet"
            href="https://cdn1.imshop.io/assets/app/b2.min.css"
          />
          <script src="https://cdn1.imshop.io/assets/app/b2.min.js" />

          {/* Icons */}
          <link
            rel="apple-touch-icon"
            sizes="57x57"
            href="/apple-icon-57x57.png"
          />
          <link
            rel="apple-touch-icon"
            sizes="60x60"
            href="/apple-icon-60x60.png"
          />
          <link
            rel="apple-touch-icon"
            sizes="72x72"
            href="/apple-icon-72x72.png"
          />
          <link
            rel="apple-touch-icon"
            sizes="76x76"
            href="/apple-icon-76x76.png"
          />
          <link
            rel="apple-touch-icon"
            sizes="114x114"
            href="/apple-icon-114x114.png"
          />
          <link
            rel="apple-touch-icon"
            sizes="120x120"
            href="/apple-icon-120x120.png"
          />
          <link
            rel="apple-touch-icon"
            sizes="144x144"
            href="/apple-icon-144x144.png"
          />
          <link
            rel="apple-touch-icon"
            sizes="152x152"
            href="/apple-icon-152x152.png"
          />
          <link
            rel="apple-touch-icon"
            sizes="180x180"
            href="/apple-icon-180x180.png"
          />
          <link
            rel="icon"
            type="image/png"
            sizes="192x192"
            href="/android-icon-192x192.png"
          />
          <link
            rel="icon"
            type="image/png"
            sizes="32x32"
            href="/favicon-32x32.png"
          />
          <link
            rel="icon"
            type="image/png"
            sizes="96x96"
            href="/favicon-96x96.png"
          />
          <link
            rel="icon"
            type="image/png"
            sizes="16x16"
            href="/favicon-16x16.png"
          />
          <link rel="manifest" href="/manifest.json" />
          <meta name="msapplication-TileColor" content="#ffffff" />
          <meta name="msapplication-TileImage" content="/ms-icon-144x144.png" />
          <meta name="theme-color" content="#ffffff" />

          <link rel="preconnect" href="https://fonts.googleapis.com" />
          <link
            rel="preconnect"
            href="https://fonts.gstatic.com"
            crossOrigin=""
          />
          <link
            href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:ital,wght@0,100;0,300;0,500;0,700;0,900;1,100;1,300;1,500;1,700;1,900&display=swap"
            rel="stylesheet"
          />
          <link
            href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,300;0,500;0,700;0,900;1,100;1,300;1,500;1,700;1,900&display=swap"
            rel="stylesheet"
          />

          {/* Yandex.Metrika counter */}
          <script
            type="text/javascript"
            dangerouslySetInnerHTML={{
              __html: `(function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
                m[i].l=1*new Date();
                for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
                k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
                (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

                ym(88910984, "init", {
                  defer: true,
                  clickmap:true,
                  trackLinks:true,
                  accurateTrackBounce:true,
                  ecommerce:"dataLayer"
                });`,
            }}
          />
          <noscript>
            <div>
              <img
                src="https://mc.yandex.ru/watch/88910984"
                style={{ position: "absolute", left: "-9999px" }}
                alt=""
              />
            </div>
          </noscript>
        </Head>
        <body>
          <Main />
          <NextScript />
        </body>
      </Html>
    );
  }
}

export default MyDocument;
