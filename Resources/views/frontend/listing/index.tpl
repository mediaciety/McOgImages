{extends file='parent:frontend/listing/index.tpl'}


{block name='frontend_index_header_meta_tags_opengraph'}
    <meta property="fb:app_id" content="{$fbAppId}" />
    <meta property="og:type" content="product.group" />
    <meta property="og:url" content="{url contoller='listing' sCategory=$sCategoryCurrent}" />
    <meta property="og:site_name" content="{{config name=sShopname}|escapeHtml}" />
    <meta property="og:title" content="{{config name=sShopname}|escapeHtml}" />
    <meta property="og:description" content="{block name='frontend_index_header_meta_description_og'}{s name='IndexMetaDescriptionStandard'}{/s}{/block}" />
    <meta property="og:image:width" content="{$ogimageDimensions[0]}" />
    <meta property="og:image:height" content="{$ogimageDimensions[1]}" />
    {foreach $ogimages as $image}
        <meta property="og:image" content="{media path=$image}" />
    {/foreach}
    <meta property="og:image:alt" content="{if $sBreadcrumb}{foreach from=$sBreadcrumb|array_reverse item=breadcrumb}{$breadcrumb.name} | {/foreach}{/if}{{config name=sShopname}|escapeHtml}"
    <meta name="twitter:card" content="website" />
    <meta name="twitter:site" content="{{config name=sShopname}|escapeHtml}" />
    <meta name="twitter:title" content="{{config name=sShopname}|escapeHtml}" />
    <meta name="twitter:description" content="{block name='frontend_index_header_meta_description_twitter'}{s name='IndexMetaDescriptionStandard'}{/s}{/block}" />
    {foreach $ogimages as $image}
        <meta property="twitter:image" content="{media path=$image}" />
    {/foreach}
{/block}