{extends file="Layout.tpl"}

{block name="style"}
    {include file="2c/OrderNew.css"}
{/block}

{block name="body"}
    <div class="header">
        <span class="icon32 icon32-info float-left"></span>
        <h2 class="float-left">{Trans::t("got_order_info")}</h2>
        <img class="float-right" src="http://pages.kangkanghui.com/a/img/homepage3/red_logo_small.png">
        <div class="clear"></div>
    </div>

    <p class="order_new_msg">{Trans::t("order_new_msg_%gname", null, ["%gname" => "<b>`$order->guest_name`</b>"])}</p>

    <div class="cell-wrapper">
        <div class="cell-thin cell-border">
            <p class="float-left">
                {Trans::t("order_num")}
                #{$order->hash_id}
                <span class="{$order_status_map[$order->status]['type']}">[{Trans::t($order_status_map[$order->status]['key'])}]</span>
            </p>
            <p class="float-right">
                <b>{Trans::t("refer_price")}</b>
                /
                <span class="price">{$refer_price}</span>
            </p>
            <div class="clear"></div>
        </div>
        <div class="cell-fat cell-border">
            <div class="form-row" id="room_name">
                <p class="font-big">{$homestay['name']}</p>
            </div>
            <div class="form-row">
                <label>{Trans::t("homestay_address")}</label>
                <p class="font-big">
                    {foreach $area_array as $loc}
                        {$loc['type_name']}
                    {/foreach}
                </p>
                <p class="font-big">{$homestay['address']}</p>
            </div>
        </div>
        <div class="cell-fat cell-border">
            <div class="form-row">
                <label>{Trans::t("check_in_room")} {Trans::t("check_in_room_num_and_nights_%n_%d", null, ['%n'=>$order->room_num, '%d'=>$order->guest_days])}</label>
                <p class="font-big">{$order->room_name}</p>
            </div>
        </div>
        <div class="cell-fat cell-border">
            <div class="form-row form-row-half">
                <label>{Trans::t("check_in_date")}</label>
                <p class="font-big">{$order->guest_date}</p>
            </div>
            <div class="form-row form-row-half">
                <label>{Trans::t("check_out_date")}</label>
                <p class="font-big">{$order->guest_checkout_date}</p>
            </div>
        </div>
        <div class="cell-fat">
            <div class="form-row form-row-half">
                <label>{Trans::t("check_in_user")}</label>
                <p class="font-big">{$order->guest_name}</p>
                <p>{$order->guest_number}{Trans::t("adult")} {$order->guest_child_number}{Trans::t("children")}</p>
                {if $order->guest_child_age > 0}
                    <p>{Trans::t("guest_child_info")} / {Trans::t('age')} {$order->guest_child_age}</p>
                {/if}
            </div>
            <div class="form-row form-row-half">
                <div class="form-row form-row-inline">
                    <label>{Trans::t("contact_phone")}</label>
                    <p>{$order->guest_telnum}</p>
                </div>
                <div class="form-row form-row-inline">
                    <label>{Trans::t("email")}</label>
                    <p>{$order->guest_mail}</p>
                </div>
                {if $order->guest_wechat}
                <div class="form-row form-row-inline">
                    <label>{Trans::t("webchat")}</label>
                    <p>{$order->guest_wechat}</p>
                </div>
                {/if}
                {if $order->guest_line_id}
                <div class="form-row form-row-inline">
                    <label>{Trans::t("guest_line_id")}</label>
                    <p>{$order->guest_line_id}</p>
                </div>
                {/if}
            </div>
        </div>
    </div>

    <div class="cell-fat cell-border">
        <div class="form-row" id="spec_services">
            <label>{Trans::t("spec_service")}</label>
            <p>{Trans::t("spec_service_explain_now")}</p>
        </div>
    </div>

    <div class="cell-fat">
        <div class="form-row">
            <label>{Trans::t("refer_price")}</label>
            <p>
                <span class="price">{$refer_price}</span>
            </p>
        </div>
    </div>
{/block}
