<?php

namespace Utils;

use Utils\ServerUtils;

class SiteUrl {
   // I'd like a "vainty" url without the `.pdf` stuff.
   // The only place that needs the `.pdf` url is the actual redirect html.
   // Loading the redirect html every time isn't optimal but `/résumé` is fun.
   public static function getResume(bool $hostedFile) {
      return $hostedFile ? ServerUtils::getHostedFile('cdcline_resume.pdf') : urlencode('résumé');
   }
}
