import Document, { Html, Head, Main, NextScript } from "next/document";
import React from "react";

class MyDocument extends Document {
  render() {
    return (
      <Html lang="ru">
        <Head>
          <meta name='title' content='Сеть аптек 120/80' />
          <meta
            key="description"
            name="description"
            content="Добро пожаловать на наш сайт - сервис для покупки лекарств и товаров в собственной аптечной сети! Наши аптеки популярны, благодаря широкому ассортименту и высокой культуре обслуживания при доступных ценах. Гарантия качества и сервисное обслуживание – основные принципы нашей работы!"
          />

          <meta name="smartbanner:title" content="АПТЕКА 120/80" />
          <meta name="smartbanner:author" content="ООО Социальная аптека" />
          <meta name="smartbanner:price" content="Бесплатно" />
          <meta name="smartbanner:price-suffix-google" content="ru.apteka120.android" />
          <meta name="smartbanner:icon-google" content="https://play-lh.googleusercontent.com/DDqqkbF38Uhzr_ooOgoCLWS8K4sgoPXb2S5qkyylq8tL0P3MhDkkmYgPW4hSxpSFwoWe=s360" />
          <meta name="smartbanner:button" content="Скачать" />
          <meta name="smartbanner:button-url-google" content="https://play.google.com/store/apps/details?id=ru.apteka120.android" />
          <meta name="smartbanner:enabled-platforms" content="android" />
          <meta name="smartbanner:line1" content="Социальная аптека" />
          <meta name="smartbanner:line2" content="[r:4.7] (23) звезды" />
          <meta name="smartbanner:line3" content="АПТЕКА 120/80" />
          <meta name="smartbanner:stars-color" content="#ff0000" />
          <link rel="stylesheet" href="https://cdn1.imshop.io/assets/app/b2.min.css" />
          <script src="https://cdn1.imshop.io/assets/app/b2.min.js" />

          <link rel="preconnect" href="https://fonts.googleapis.com" />
          <link
            rel="preconnect"
            href="https://fonts.gstatic.com"
            crossOrigin=""
          />
          <link
            href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700;1,900&display=swap"
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
