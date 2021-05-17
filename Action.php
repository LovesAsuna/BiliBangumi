<?php

class BilibiliAPI {
    private $uid;
    private $cookies;
    private $pageSize;

    private static $instance;

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new \BilibiliAPI();
        }
        return self::$instance;
    }

    private function __construct() {
        $config = Typecho_Widget::widget('Widget_Options')->plugin('BiliBangumi');
        if ($config->pageSize == 0) {
            $this->pageSize = 16;
        } else {
            $this->pageSize = $config->pageSize;
        }
        $this->uid = $config->userID;
        $this->cookies = $config->cookie;
    }


    public function get_the_bgm_items($page = 1) {
        $url = 'https://api.bilibili.com/x/space/bangumi/follow/list?type=1&pn=' . $page . '&ps='. $this->pageSize . '&follow_status=0&vmid=' . $this->uid;
        $options = array(
            'http' => array(
                'method' => "GET",
                'header' => "Cookie: $this->cookies\r\n" .
                    "Host: api.bilibili.com\r\n" .
                    "User-Agent: Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.102011-10-16 20:23:10\r\n"
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $bangumiData = json_decode($result)->data;
        return json_encode($bangumiData);
    }

    public function get_bgm_items($page = 1) {
        if ($this->uid == 0) {
            return "<div class='error' style='width: 260px; margin: 0 auto'>没有填写UID或者Cookie，请检查插件设置</div>";
        }
        $bgm = json_decode($this->get_the_bgm_items($page), true);
        $totalpage = $bgm["total"] / $this->pageSize;
        if ($totalpage - $page < 0) {
            $next = '<span>共追番' . $bgm["total"] . '部，继续加油吧！٩(ˊᗜˋ*)و</span>';
        } else {
            $next = '<a class="bangumi-next" href="javascript:;" onclick="reload(\'' . Helper::options()->siteUrl . 'index.php/BiliBangumi?page=' . ++$page . '\')"><i class="fa fa-bolt" aria-hidden="true"></i> NEXT </a>';
        }
        $lists = $bgm["list"];
        $html = <<<EOF
<article class="post-item">
    <section class="bangumi">
        <div class="row">
EOF;
        foreach ((array)$lists as $list) {
            if (preg_match('/看完/m', $list["progress"], $matches_finish)) {
                $percent = 100;
            } else {
                if ($list["new_ep"] == null) {
                    continue;
                }
                preg_match('/第(\d+)./m', $list['progress'], $matches_progress);
                preg_match('/第(\d+)./m', $list["new_ep"]['index_show'], $matches_new);
                if (isset($matches_progress[1])) {
                    $progress = is_numeric($matches_progress[1]) ? $matches_progress[1] : 0;
                } else {
                    $progress = 0;
                }
                if (isset($matches_new[1])) {
                    $total = is_numeric($matches_new[1]) ? $matches_new[1] : $list['total_count'];
                } else {
                    $total = $list['total_count'];
                }
                $percent = $progress / $total * 100;
            }
            $html .= '<div class="column">
                <a class="bangumi-item" href="https://bangumi.bilibili.com/anime/' . $list['season_id'] . '/" target="_blank" rel="nofollow">
                <img class="bangumi-image" src="' . str_replace('http://', 'https://', $list['cover']) . '"/>
                    <div class="bangumi-info">
                        <h3 class="bangumi-title" style="color: white" title="' . $list['title'] . '">' . $list['title'] . '</h2>
                        <div class="bangumi-summary" style="color: white"> ' . mb_substr($list['evaluate'], 0, 70) . ' </div>
                        <div class="bangumi-status">
                            <div class="bangumi-status-bar" style="width: ' . $percent . '%"></div>
                            <p>' . $list['new_ep']['index_show'] . '</p>         
                        </div>
                    </div>
                </a>
            </div>';
        }
        $html .= '</div><br><div id="bangumi-pagination">' . $next . '</div>';
        $html .= <<<EOF
            </section>
	</article>
EOF;
        return $html;
    }
}

class BiliBangumi_Action extends Widget_Abstract_Contents implements Widget_Interface_Do {
    public function action() {
        $bilibiliAPI = BilibiliAPI::GetInstance();
        if ($_GET['page'] == '')
            $page = 1;
        else
            $page = $_GET['page'];
        echo $bilibiliAPI->get_bgm_items($page);
    }

}

