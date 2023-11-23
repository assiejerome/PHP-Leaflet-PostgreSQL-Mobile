self.addEventListener("install", function (event) {
  event.waitUntil(
    caches.open("pwa").then(function (cache) {
      return cache.addAll(["/"]);
    })
  );
});

self.addEventListener("fetch", function (event) {
  if (event.request.url.indexOf("chrome-extension") === -1) {
    event.respondWith(
      caches.open("pwa").then(function (cache) {
        return cache.match(event.request).then(function (response) {
          cache.addAll([event.request.url]);

          if (response) {
            return response;
          }

          return fetch(event.request);
        });
      })
    );
  } else {
    console.log("not caching : " + event.request.url);
  }
});
