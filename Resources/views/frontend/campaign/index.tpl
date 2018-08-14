{extends file="parent:frontend/campaign/index.tpl"}

{block name='frontend_index_header_meta_tags_opengraph'}
    <meta property="fb:app_id" content="{$fbAppId}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://{$smarty.server.HTTP_HOST}{$smarty.server.REQUEST_URI}" />
    <meta property="og:site_name" content="{{config name=sShopname}|escapeHtml}" />
    <meta property="og:title" content="{{config name=sShopname}|escapeHtml}" />
    <meta property="og:description" content="{block name='frontend_index_header_meta_description_og'}{s name='IndexMetaDescriptionStandard'}{/s}{/block}" />
    {foreach $ogimages as $image}
        <meta property="og:image" content="{media path=$image}" />
    {/foreach}
    <meta property="og:image:alt" content="{$seo_title}"/>

    <meta name="twitter:card" content="website" />
    <meta name="twitter:site" content="{{config name=sShopname}|escapeHtml}" />
    <meta name="twitter:title" content="{{config name=sShopname}|escapeHtml}" />
    <meta name="twitter:description" content="{block name='frontend_index_header_meta_description_twitter'}{s name='IndexMetaDescriptionStandard'}{/s}{/block}" />

    {foreach $ogimages as $image}
        <meta property="twitter:image" content="{media path=$image}" />
    {/foreach}
{/block}