#!/usr/bin/env php
<?php
// Sorry, this probably isn't as neat as you expected. But this is our test suite. :P

require_once dirname(__FILE__).'/runner.php';
require_once dirname(__FILE__).'/test_content_type.php';
require_once dirname(__FILE__).'/test_header_regex.php';
require_once dirname(__FILE__).'/test_exact_match.php';
require_once dirname(__FILE__).'/test_regex.php';
require_once dirname(__FILE__).'/test_response_code.php';
require_once dirname(__FILE__).'/test_response_regex.php';
require_once dirname(__FILE__).'/test_tag_unit.php';

function id($a) {
  return $a;
}

$runner = new DaGdTestRunner();

$test_url = 'http://dagd.local/';
if (count($argv) > 1) {
  $test_url = $argv[1];
}

$runner->setBaseUrl($test_url);

/*********** / ***********/
$runner->arm(
  id(new DaGdContentTypeTest('/', 'text/plain'))
    ->addGroup('shorten')
    ->setAccept('text/plain'));

$runner->arm(
  id(new DaGdContentTypeTest('/', 'text/html'))
    ->addGroup('shorten')
    ->setAccept('text/html'));

$runner->arm(
  id(new DaGdRegexTest('/', '@various conditions@'))
    ->addGroup('shorten'));

$runner->arm(
  id(new DaGdRegexTest('/?text=0', '@<form@'))
    ->addGroup('shorten'));

$runner->arm(
  id(new DaGdRegexTest('/', '@<form@'))
    ->addGroup('shorten')
    ->setAccept('text/html'));

/*********** /ip ***********/

$runner->arm(
  id(new DaGdRegexTest('/ip', '@(?:[0-9]+\.){3}[0-9]+$@'))
    ->addGroup('ip'));
$runner->arm(
  id(new DaGdRegexTest('/ip', '@(?:[0-9]+\.){3}[0-9]@'))
    ->addGroup('ip')
    ->setAccept('text/html'));

/*********** /ua ***********/

$runner->arm(
  id(new DaGdRegexTest('/ua', '@^monkey$@'))
    ->addGroup('ua')
    ->setUserAgent('monkey'));
$runner->arm(
  id(new DaGdRegexTest('/ua', '@>monkey@'))
    ->addGroup('ua')
    ->setUserAgent('monkey')
    ->setAccept('text/html'));

/*********** /ec/CodeBlock ***********/

$runner->arm(
  id(new DaGdRegexTest('/ec/CodeBlock', '@^[0-9]+$@'))
    ->addGroup('ec'));
$runner->arm(
  id(new DaGdRegexTest('/ec/CodeBlock?lang=en', '@^[0-9]+$@'))
    ->addGroup('ec'));
$runner->arm(
  id(new DaGdRegexTest('/ec/CodeBlock?lang=fr', '@^[0-9]+$@'))
    ->addGroup('ec'));

/************ /w/xxxxxxx ************/

/* // Ensure that whois is functioning. */
$runner->arm(
  id(new DaGdRegexTest('/w/google.com', '@ns1.google.com@'))
    ->addGroup('whois'));
//$runner->arm(
//  id(new DaGdRegexTest('/w/da.gd', '@bill.ns.cloudflare.com@i')));
$runner->arm(
  id(new DaGdRegexTest('/w/4.2.2.2', '@Level 3 Parent@'))
    ->addGroup('whois'));
$runner->arm(
  id(new DaGdRegexTest('/w/74.94.18.108', '@OrgAbusePhone@'))
    ->addGroup('whois'));
$runner->arm(
  id(
    new DaGdRegexTest(
      '/w/2001:470:8:624:211c:aaaa:1111:1111',
      '@Hurricane Electric@'))
    ->addGroup('whois'));
$runner->arm(
  id(
    new DaGdRegexTest(
      '/w/2001:470:8:624:211c:aaaa:1111:1111',
      '@Hurricane Electric@'))
    ->addGroup('whois'));
$runner->arm(
  id(
    new DaGdRegexTest(
      '/w/trees.network',
      '@Registrar WHOIS Server: whois.1api.net@'))
    ->addGroup('whois'));
$runner->arm(
  id(new DaGdRegexTest('/w/trees.network','@1API GmbH@'))
    ->addGroup('whois'));
$runner->arm(
  id(
    new DaGdRegexTest(
      '/w/donuts.co',
      '@Registrant Organization: Donuts Inc.@'))
    ->addGroup('whois'));
$runner->arm(
  id(
    new DaGdRegexTest(
      '/w/usi.edu',
      '@University of Southern Indiana@'))
    ->addGroup('whois'));

/************ /headers/xxxxxxx ************/

$runner->arm(
  id(new DaGdRegexTest('/headers', '@Host: @'))
    ->addGroup('headers'));
$runner->arm(
  id(new DaGdRegexTest('/headers', '@X-DaGd-Proxy: @', true))
    ->addGroup('headers'));
$runner->arm(
  id(new DaGdRegexTest('/headers/google.com', '@Server: gws@'))
    ->addGroup('headers'));
$runner->arm(
  id(new DaGdRegexTest('/headers/http://google.com/', '@Server: gws@'))
    ->addGroup('headers'));

/************ /up/xxxxxxx ************/
$runner->arm(
  id(new DaGdRegexTest('/up/google.com', '@^200$@'))
    ->addGroup('up'));
$runner->arm(
  id(new DaGdRegexTest('/up/https://encrypted.google.com/', '@^200$@'))
    ->addGroup('up'));
$runner->arm(
  id(new DaGdRegexTest('/up/http://google.com/404', '@^404$@'))
    ->addGroup('up'));

/************ /et/xxxxxxx ************/
$runner->arm(
  id(new DaGdResponseCodeTest('/et/750009720', 302))
    ->addGroup('et'));

/************ /host/xxxxxxx ************/
$runner->arm(
  id(new DaGdRegexTest('/host/ipv6.google.com', '@:@'))
    ->addGroup('host'));
$runner->arm(
  id(new DaGdRegexTest('/host/google.com', '@[0-9]\.@'))
    ->addGroup('host'));
$runner->arm(
  id(new DaGdRegexTest('/host/google.com?noipv6', '@:@', true))
    ->addGroup('host'));
$runner->arm(
  id(new DaGdRegexTest('/host/4.2.2.2', '@b.resolvers.Level3.net@'))
    ->addGroup('host'));

/************ /break/ ************/
$runner->arm(
  id(new DaGdResponseCodeTest('/break', 500))
    ->addGroup('break'));
$runner->arm(
  id(new DaGdRegexTest('/break', '@An error has occurred@'))
    ->addGroup('break'));

/************ /c/store/xxxxxxx/xxxxxxx ************/
$runner->arm(
  id(
    new DaGdResponseCodeTest(
      '/c/store/g/https://www.google.com/search?q=$PARAMETERS',
      200))
    ->setPreparatory(true)
    ->setTolerateFailure(true)
    ->addGroup('commander'));
$runner->arm(
  id(
    new DaGdResponseCodeTest(
      '/c/store/g/https://www.google.com/search?q=$PARAMETERS',
      400))
    ->addGroup('commander'));
$runner->arm(
  id(
    new DaGdRegexTest(
      '/c/store/g1/https://www.google.com/search?q=$PARAMETERS',
      '@Success@'))
    ->setPreparatory(true)
    ->setTolerateFailure(true)
    ->addGroup('commander'));
$runner->arm(
  id(
    new DaGdRegexTest(
      '/c/store/g1/https://www.google.com/search?q=$PARAMETERS',
      '@already been defined@'))
    ->addGroup('commander'));

/************ /c/xxxxxxx/xxxxxxx ************/
$runner->arm(
  id(new DaGdResponseCodeTest('/c/g/foobar', 302))
    ->addGroup('commander'));
$runner->arm(
  id(new DaGdResponseCodeTest('/c/g%20foobar', 302))
    ->addGroup('commander'));
$runner->arm(
  id(new DaGdResponseCodeTest('/c/nonexistent/foobar', 400))
    ->addGroup('commander'));
$runner->arm(
  id(new DaGdRegexTest('/c/nonexistent/foobar', '@was not found@'))
    ->addGroup('commander'));

/************ /c/[xxxxxxx] ************/
$runner->arm(
  id(new DaGdRegexTest('/c/', '@Redirect@'))
    ->addGroup('commander'));
$runner->arm(
  id(new DaGdRegexTest('/c/json', '@{"g":@'))
    ->addGroup('commander'));
$runner->arm(
  id(new DaGdRegexTest('/c/json/', '@"g1":@'))
    ->addGroup('commander'));
$runner->arm(
  id(new DaGdContentTypeTest('/c/json/', 'application/json'))
    ->addGroup('commander')
    ->setAccept('text/plain'));
$runner->arm(
  id(new DaGdContentTypeTest('/c/json', 'application/json'))
    ->addGroup('commander')
    ->setAccept('text/html'));
$runner->arm(
  id(new DaGdContentTypeTest('/c', 'text/html'))
    ->addGroup('commander')
    ->setAccept('text/html'));
$runner->arm(
  id(new DaGdContentTypeTest('/c/', 'text/html'))
    ->addGroup('commander')
    ->setAccept('text/html'));
$runner->arm(
  id(new DaGdContentTypeTest('/c', 'text/plain'))
    ->addGroup('commander')
    ->setAccept('text/plain'));
$runner->arm(
  id(new DaGdContentTypeTest('/c/', 'text/plain'))
    ->addGroup('commander')
    ->setAccept('text/plain'));

/************ /image/xxxxxxx/[xxxxxxx] ************/
$runner->arm(
  id(new DaGdContentTypeTest('/image/200x200', 'image/png'))
    ->addGroup('image')
    ->setAccept('text/html'));
$runner->arm(
  id(new DaGdContentTypeTest('/image/200x200', 'image/png'))
    ->addGroup('image')
    ->setAccept('text/plain'));
$runner->arm(
  id(new DaGdContentTypeTest('/image/10x10.jpg', 'image/jpeg'))
    ->setAccept('text/plain'));
$runner->arm(
  id(new DaGdContentTypeTest('/image/30x20.gif?bgcolor=333333', 'image/gif'))
    ->addGroup('image')
    ->setAccept('text/html'));
$runner->arm(
  id(new DaGdResponseCodeTest('/image/300', 400))
    ->addGroup('image'));
$runner->arm(
  id(new DaGdResponseCodeTest('/image/300000000x1212121221', 400))
    ->addGroup('image'));

/************ /status/xxxxxxx/[xxxxxxx] ************/
$runner->arm(
  id(new DaGdResponseCodeTest('/status/400', 400))
    ->addGroup('status'));
$runner->arm(
  id(new DaGdResponseCodeTest('/status/403', 403))
    ->addGroup('status'));
$runner->arm(
  id(new DaGdResponseCodeTest('/status/321/hi', 321))
    ->addGroup('status'));
$runner->arm(
  id(new DaGdResponseRegexTest('/status/321/hi', '@321 hi$@'))
    ->addGroup('status'));

/************ /isp/[xxxxxxx] ************/
$runner->arm(
  id(
    new DaGdRegexTest(
      '/isp/127.0.0.1',
      '@^Internet Assigned Numbers Authority@'))
    ->addGroup('isp'));
$runner->arm(
  id(new DaGdRegexTest('/isp/69.171.237.16', '@Facebook, Inc\.@'))
    ->addGroup('isp'));
$runner->arm(
  id(new DaGdRegexTest('/isp/1.1.1.1', '@Cloudflare@'))
    ->addGroup('isp'));
$runner->arm(
  id(
    new DaGdRegexTest(
      '/isp/2607:f8b0:4000:812::200e',
      '@Google@'))
    ->addGroup('isp'));

/************ /help ************/
$runner->arm(
  id(new DaGdRegexTest('/help', '@pixels: <a href="/image/200x400/png"@'))
  ->addGroup('help')
  ->addGroup('deprecated')
  ->setAccept('text/html'));
$runner->arm(
  id(
    new DaGdRegexTest(
      '/help?url_prefix=dagd%20&url_separator=%20&url_request_sep=%20--',
      '@image 200x400 png@'))
    ->setAccept('text/plain')
    ->addGroup('help')
    ->addGroup('deprecated'));

/************ /dns/[xxxxxxx] ************/
$runner->arm(
  id(new DaGdRegexTest('/dns/google.com', '@IN NS@'))
    ->addGroup('dns'));

$runner->arm(
  id(new DaGdRegexTest('/dns/google.com', '@IN A@'))
    ->addGroup('dns'));

/************ /s/[xxxxxxx] ************/
$runner->arm(
  id(
    new DaGdRegexTest(
      '/s?url=http://google.com&shorturl=g',
      '@/g@'))
    ->setPreparatory(true)
    ->addGroup('shorten'));

$runner->arm(
  id(
    new DaGdRegexTest(
      '/s?url=http://facebook.com/&shorturl=fbook',
      '@/fbook@'))
    ->setPreparatory(true)
    ->addGroup('shorten'));

$runner->arm(
  id(
    new DaGdRegexTest(
      '/s?url=http://example.com/&shorturl=example',
      '@/example@'))
      ->setAccept('text/plain')
    ->setPreparatory(true)
    ->addGroup('shorten'));

$runner->arm(
  id(
    new DaGdRegexTest(
      '/s?url=http://2.example.com/&shorturl=2example',
      '@/2example@'))
      ->setAccept('text/html')
    ->setPreparatory(true)
    ->addGroup('shorten'));

$runner->arm(
  id(new DaGdRegexTest('/coshorten/g', '@http://google.com@'))
    ->addGroup('shorten')
    ->addGroup('coshorten'));

$runner->arm(
  id(new DaGdResponseCodeTest('/g', 302))
    ->addGroup('shorten'));
$runner->arm(
  id(new DaGdResponseCodeTest('/g/foo', 302))
    ->addGroup('shorten'));
$runner->arm(
  id(new DaGdResponseCodeTest('/fbook', 302))
    ->addGroup('shorten'));

$runner->arm(
  id(new DaGdHeaderRegexTest('/g', 'Location', '@^http://google.com$@'))
    ->addGroup('shorten'));
$runner->arm(
  id(
    new DaGdHeaderRegexTest(
      '/g/?foo=bar',
      'Location',
      '@^http://google.com\?foo=bar$@')));
$runner->arm(
  id(
    new DaGdHeaderRegexTest(
      '/g/foo',
      'Location',
      '@^http://google.com/foo$@'))
    ->addGroup('shorten'));

$runner->arm(
  id(
    new DaGdRegexTest(
      '/s?url=https://some.spam.url&shorturl=spam',
      '@Blacklisted original URL@'))
    ->addGroup('shorten'));

$runner->arm(
  id(new DaGdResponseCodeTest('/s?url=https://some.spam.url', 400))
    ->addGroup('shorten'));

/************ /stats/ ************/
$runner->arm(
  id(new DaGdResponseCodeTest('/stats/g', 200))
  ->addGroup('stats'));

$runner->arm(
  id(
    new DaGdRegexTest(
      '/stats/g',
      '@accesses: \d@'))
    ->addGroup('stats'));

/************ ?strip ************/
$runner->arm(
  id(new DaGdRegexTest('/ip', '@\n$@'))
    ->addGroup('ip')
    ->addGroup('strip'));
$runner->arm(
  id(new DaGdRegexTest('/ip?strip=0', '@\n$@'))
    ->addGroup('ip')
    ->addGroup('strip'));
$runner->arm(
  id(new DaGdRegexTest('/ip?strip', '@[0-9]$@'))
    ->addGroup('ip')
    ->addGroup('strip'));
$runner->arm(
  id(new DaGdRegexTest('/ip?strip=1', '@[0-9]$@'))
    ->addGroup('ip')
    ->addGroup('strip'));

/************ /roll/ ************/
$runner->arm(
  id(new DaGdResponseCodeTest('/roll/3d', 404))
    ->addGroup('roll'));
$runner->arm(
  id(new DaGdRegexTest('/roll/d9', '@^[0-9]$@'))
    ->addGroup('roll'));
$runner->arm(
  id(new DaGdRegexTest('/roll/3d1', '@^3$@'))
    ->addGroup('roll'));
$runner->arm(
  id(new DaGdRegexTest('/roll/3d1+3', '@^6$@'))
    ->addGroup('roll'));
$runner->arm(
  id(new DaGdRegexTest('/roll/3d1-1', '@^2$@'))
    ->addGroup('roll'));
$runner->arm(
  id(new DaGdRegexTest('/roll/3d10', '@^\d+$@'))
    ->addGroup('roll'));

/************ /leftpad/ ************/
$runner->arm(
  id(new DaGdRegexTest('/leftpad/10/z/foo', '@^z{7}foo$@'))
    ->addGroup('leftpad'));

/************ ?darkmode cookie ************/
$runner->arm(
  id(new DaGdHeaderRegexTest('/?darkmode', 'Set-Cookie', '@^darkmode=true;@'))
    ->addGroup('darkmode')
    ->addGroup('session')
    ->setAccept('text/html'));

/************ /cow/ ************/
$expected = <<<EOD
 ________________________
< I am moo, hear me roar >
 ------------------------
  \
   \   \\\\_\\\\_    _/_/
    \      \\\\__/
           (AA)\\\\_______
           (__)\\\\       )\\\/\\\
            <> ||----w |
               ||     ||

EOD;
$runner
  ->arm(
    id(
      new DaGdExactMatchTest(
        '/cow?text=I%20am%20moo,%20hear%20me%20roar&cow=moose&eyes=AA&tongue=<>',
        $expected))
    ->addGroup('cow'));

/************ Tag unit tests ************/
$runner->arm(
  id(
    new DaGdTagUnitTest(
      tag('h1', 'hello'),
      '<h1>hello</h1>'))
  ->addGroup('unit')
  ->addGroup('tag'));

$runner->arm(
  id(
    new DaGdTagUnitTest(
      tag('br'),
      '<br/>'))
  ->addGroup('unit')
  ->addGroup('tag'));

$runner->arm(
  id(
    new DaGdTagUnitTest(
      tag('a', 'quote "quote" quote', array('href' => '/"foo"')),
      '<a href="/&quot;foo&quot;">quote "quote" quote</a>'))
  ->addGroup('unit')
  ->addGroup('tag'));

$runner->arm(
  id(
    new DaGdTagUnitTest(
      tag('p', 'Foo & Bar, LLC.'),
      '<p>Foo &amp; Bar, LLC.</p>'))
  ->addGroup('unit')
  ->addGroup('tag'));

$runner->arm(
  id(
    new DaGdTagUnitTest(
      tag(
        'p',
        array(
          'multiple',
          'strings',
          tag(
            'b',
            'and this is bold'
          )
        )
      ),
      '<p>multiplestrings<b>and this is bold</b></p>'))
  ->addGroup('unit')
  ->addGroup('tag'));

$runner->arm(
  id(
    new DaGdTagUnitTest(
      tag(
        'p',
        array(
          'multiple',
          'strings',
          tag(
            'b',
            array(
              'and this is bold',
              tag(
                'i',
                'and this is bold and italic'
              ),
            )
          ),
        )
      ),
      '<p>multiplestrings<b>and this is bold<i>and this is '.
      'bold and italic</i></b></p>'))
  ->addGroup('unit')
  ->addGroup('tag'));

$runner->run();

echo chr(27)."[1;32m Passed        : ".$runner->getPasses().chr(27)."[0m"."\n";
echo chr(27)."[1;31m Failed        : ".$runner->getFailures().chr(27)."[0m"."\n";
echo chr(27)."[1;33m Tolerated Fail: ".$runner->getToleratedFailures().chr(27).
  "[0m"."\n";

exit($runner->getReturnCode());
