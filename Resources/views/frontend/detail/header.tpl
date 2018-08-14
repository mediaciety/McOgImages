{extends file='parent:frontend/index/header.tpl'}


{block name="frontend_index_header_meta_tags_opengraph"}
    <meta property="fb:app_id" content="{$fbAppId}" />
    <meta property="og:type" content="product" />
    <meta property="og:site_name" content="{{config name=sShopname}|escapeHtml}" />
    <meta property="og:url" content="{url sArticle=$sArticle.articleID title=$sArticle.articleName}" />
    <meta property="og:title" content="{$sArticle.articleName|escapeHtml}" />
    <meta property="og:description" content="{$sArticle.description_long|strip_tags|trim|truncate:240|escapeHtml}" />
    <meta property="og:image" content="{media path=$sArticle.ogimage}" />
    <meta property="og:image:alt" content="{$sArticle.articleName|escapeHtml}" />
    <meta property="og:image:width" content="{$ogimageDimensions[0]}" />
    <meta property="og:image:height" content="{$ogimageDimensions[1]}" />


    <meta property="product:brand" content="{$sArticle.supplierName|escapeHtml}" />
    <meta property="product:price" content="{$sArticle.price}" />
    <meta property="product:product_link" content="{url sArticle=$sArticle.articleID title=$sArticle.articleName}" />

    <meta name="twitter:card" content="product" />
    <meta name="twitter:site" content="{{config name=sShopname}|escapeHtml}" />
    <meta name="twitter:title" content="{$sArticle.articleName|escapeHtml}" />
    <meta name="twitter:description" content="{$sArticle.description_long|strip_tags|trim|truncate:240|escapeHtml}" />
    <meta name="twitter:image" content="{media path=$sArticle.ogimage}" />
{/block}