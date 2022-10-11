<?php

namespace Utils;

use Utils\ServerUtils;

class SiteUrl {
   public static function getResume() {
      return ServerUtils::getHostedFile('cdcline_resume.pdf');
   }
}
