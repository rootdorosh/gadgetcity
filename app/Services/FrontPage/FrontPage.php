<?php
declare(strict_types=1);

namespace App\Services\FrontPage;

use App;
use App\Modules\Core\Models\{
    Domain,
    Page,
    Seo,
    Banner
};
use App\Modules\Core\Services\Fetch\{
    DomainFetchService,
    PageFetchService,
    SeoFetchService,
    BannerFetchService
};
use Exception;

class FrontPage
{
    /*
     * @var Domain
     */
    private $domain;

    /*
     * @var array
     */
    private $banners;

    /*
     * @var Seo
     */
    private $seo;

    /*
     * @var Page
     */
    private $page;

    /*
     * @var string
     */
    private $keywords;

    /*
     * @var string
     */
    private $title;
    
    /*
     * @var string
     */
    private $h1;
            
    /*
     * @var string
     */
    private $description;
        
    /*
     * @var string
     */
    private $headerText;
        
    /*
     * @var string
     */
    private $footerText;
        
    /*
     * @var array
     */
    private $breadcrumbsParams = [];
    
    /*
     * @return self
     */
    public function getInstance(): self
    {
        return $this;
    }

    /*
     * @param Domain $domain
     * @return self
     */
    public function setDomain(Domain $domain): self
    {
        $this->domain = $domain;        
        return $this;
    }
    
    /*
     * @return Domain
     * @throw Exception
     */
    public function getDomain(): Domain
    {
        if ($this->domain === null) {
            $alias = request()->getHttpHost();
            $domain = (new DomainFetchService)->byAlias($alias);
            if ($domain === null) {
                throw new Exception("domain $alias not found !!!");
            }
            
            $this->domain = $domain;
        }
        
        return $this->domain;
    }
    
    /*
     * @return array
     */
    public function getUrlMap(): array
    {
        return (new SeoFetchService)->getDomainUrlMap($this->getDomain());
    }
    
    /*
     * @return Page
     * @param string $alias
     * @throw Exception
     */
    public function getPage(string $alias): Page
    {
        if ($this->page === null) {
            $page = (new PageFetchService)->byAlias($alias);
            if ($page === null) {
                throw new Exception("Page $alias not found !!!");
            }
            
            $this->page = $page;
        }
        
        return $this->page;
    }

    /*
     * @param string $alias
     * @param array $params
     * @return Seo|null
     * @throw Exception
     */
    public function getSeo(string $alias, array $params = []):? Seo
    {
        if ($this->seo === null) {
            $domain = $this->getDomain();
            $page = $this->getPage($alias);
            
            $seo = (new SeoFetchService)->byDomainAndPage($domain, $page);
            
            if (!empty($params)) {
                foreach ((new Seo)->fillable as $attr) {
                    foreach ($params as $key => $val) {
                        $seo->$attr = str_replace("[{$key}]", $val, $seo->$attr);
                    }
                }
            }
            
            $this->seo = $seo;
        }
        
        return $this->seo;
    }

    /*
     * @param string $pageAlias
     * @param string $attribute
     * @param array $params
     * @return string|null
     */
    public function getSeoParamByPage(string $pageAlias, string $seoAttribute, array $params = []):? string
    {
        return (new SeoFetchService)->getSeoParamByPage($this->getDomain(), $pageAlias, $seoAttribute, $params);
    }

    /*
     * @param string $alias
     * @return array
     * @throw Exception
     */
    public function getBanners(string $alias): array
    {
        if ($this->banners === null) {
            $domain = $this->getDomain();
            $page = $this->getPage($alias);
            
            $this->banners = (new BannerFetchService)->byDomainAndPage($domain, $page);
        }
        
        return $this->banners;
    }

    /*
     * @return void
     */
    public function setLocale()
    {
        App::setLocale($this->getDomain()->lang);
    }
            
    /*
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return !empty($this->title) ? $this->title : $this->seo->title;
    }
    
    /*
     * @param string $value
     * @return self
     */
    public function setTitle(string $value): self
    {
        $this->title = $value;
        return $this;
    }
    
    /*
     * @return string|null
     */
    public function getKeywords(): ?string
    {
        return !empty($this->keywords) ? $this->keywords : $this->seo->keywords;
    }
    
    /*
     * @param string $value
     * @return self
     */
    public function setKeywords(string $value): self
    {
        $this->keywords = $value;
        return $this;
    }
    
    /*
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return !empty($this->description) ? $this->description : $this->seo->description;
    }
    
    /*
     * @param string $value
     * @return self
     */
    public function setDescription(string $value): self
    {
        $this->description = $value;
        return $this;
    }
    
    /*
     * @return string|null
     */
    public function getHeaderText(): ?string
    {
        return !empty($this->headerText) ? $this->headerText : $this->seo->header_text;
    }
    
    /*
     * @param string $value
     * @return self
     */
    public function setHeaderText(string $value): self
    {
        $this->headerText = $value;
        return $this;
    }
    
    /*
     * @return string|null
     */
    public function getFooterText(): ?string
    {
        return !empty($this->footerText) ? $this->footerText : $this->seo->footer_text;
    }
    
    /*
     * @param string $value
     * @return self
     */
    public function setFooterText(string $value): self
    {
        $this->footerText = $value;
        return $this;
    }
    

    /*
     * @return string|null
     */
    public function getH1(): ?string
    {
        return !empty($this->h1) ? $this->h1 : $this->seo->seo_h1;
    }
        
    /*
     * @param string|null $title
     * @param string|null $label
     * @param string|null $url
     * @return void
     */
    public function setBreadcrumbs($title, $label, $url = null)
    {
        $this->breadcrumbsParams[] = compact('url', 'title', 'label');
    }
    
    /*
     * @return array
     */
    public function getBreadcrumbs(): array
    {
        return $this->breadcrumbsParams;
    }

    /*
     * @return Domain $domain
     * @return string
     */
    public function getCurrentUrlByDomain(Domain $domain): string
    {
        $seo = (new SeoFetchService)->byDomainAndPageAlias($domain, request()->route()->getName());
        $url = $domain->alias;
        if ($seo !== null) {
            $url .= $seo->url;
            foreach (request()->route()->parameters as $key => $val) {
                $url = str_replace('{' . $key . '}', $val, $url);
            }
        }
        
        return str_replace('//', '/', $url);
    }
}