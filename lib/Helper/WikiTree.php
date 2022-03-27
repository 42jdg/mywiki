<?php
namespace OCA\MyWiki\Helper;

class WikiTree {
    private array $wikiPages;

    public function __construct(?array $wikiPages) {
        $this->wikiPages = $wikiPages??[];
    }

    public function getWikiPages(): array {
        return $this->wikiPages;
    }

    public function get($id): ?WikiTreePage {
        $wikiTreePage = null;
        foreach($this->wikiPages as $page) {
            if ( $page['id']==$id) {
                $wikiTreePage = new WikiTreePage();
                $wikiTreePage->id = $page['id'];
                $wikiTreePage->pid = $page['pid'];
                $wikiTreePage->title = $page['title'];
                $wikiTreePage->sort = $page['sort'];
                break;
            }
        }
        return $wikiTreePage;
    }

    private function countChilds($id): int {
        $n = 0;
        foreach($this->wikiPages as $page) {
            if ($page['pid']==$id) {
                $n++;
            }
        }
        return $n;
    }

    public function set(WikiTreePage $wikiTreePage): WikiTree {
        if ( $this->get($wikiTreePage->id) === null ) {
            $this->add($wikiTreePage);
        } else {
            $this->modify($wikiTreePage);
        }
        return $this;
    }

    private function add(WikiTreePage $wikiTreePage): WikiTree {
        if ($wikiTreePage->sort<=0) {
            $wikiTreePage->sort = $this->countChilds($wikiTreePage->pid) + 1;
        }
        $this->wikiPages[] = [
            "id"=>$wikiTreePage->id,
            "pid"=>$wikiTreePage->pid,
            "title"=>$wikiTreePage->title,
            "sort"=>$wikiTreePage->sort
        ];
        return $this;
    }

    private function modify(WikiTreePage $wikiTreePage): WikiTree {
        foreach($this->wikiPages as &$page) {
            if ( $page['id']==$wikiTreePage->id) {
                $page['pid'] = $wikiTreePage->pid;
                $page['title'] = $wikiTreePage->title;
                $page['sort'] = $wikiTreePage->sort;
            }
        }
        return $this;
    }

    public function del($id): ?WikiTree {
        $pages = [];
        foreach($this->wikiPages as $k => $page) {
            if ($page['id']!=$id && $page['pid']!=$id) {
                $pages[] = $page;
            }
        }
        $this->wikiPages = $pages;
        return $this;
    }
}