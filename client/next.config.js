module.exports = {
  webpack(config) {
    config.module.rules.push({
      test: /\.svg$/i,
      issuer: { and: [/\.(js|ts)x?$/] },
      use: [
        {
          loader: '@svgr/webpack',
          options: {
            prettier: false,
            svgo: true,
            svgoConfig: {
              plugins: [{
                name: 'preset-default',
                params: {
                  overrides: { removeViewBox: false },
                },
              }]
            },
            titleProp: true,
          },
        },
      ],
    });

    return config;
  },

  images: {
    domains: ['api.pharm.test', 'api.120на80.рф', 'api.xn--12080-6ve4g.xn--p1ai'],
    // formats: ['image/webp', 'image/jpeg', 'image/png', 'image/gif'],
  },
  
  experimental: {
    outputStandalone: true,
  }
};
