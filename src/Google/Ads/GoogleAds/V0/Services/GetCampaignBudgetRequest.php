<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/ads/googleads/v0/services/campaign_budget_service.proto

namespace Google\Ads\GoogleAds\V0\Services;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Request message for [CampaignBudgetService.GetCampaignBudget][google.ads.googleads.v0.services.CampaignBudgetService.GetCampaignBudget].
 *
 * Generated from protobuf message <code>google.ads.googleads.v0.services.GetCampaignBudgetRequest</code>
 */
class GetCampaignBudgetRequest extends \Google\Protobuf\Internal\Message
{
    /**
     * The resource name of the campaign budget to fetch.
     *
     * Generated from protobuf field <code>string resource_name = 1;</code>
     */
    private $resource_name = '';

    public function __construct() {
        \GPBMetadata\Google\Ads\GoogleAds\V0\Services\CampaignBudgetService::initOnce();
        parent::__construct();
    }

    /**
     * The resource name of the campaign budget to fetch.
     *
     * Generated from protobuf field <code>string resource_name = 1;</code>
     * @return string
     */
    public function getResourceName()
    {
        return $this->resource_name;
    }

    /**
     * The resource name of the campaign budget to fetch.
     *
     * Generated from protobuf field <code>string resource_name = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setResourceName($var)
    {
        GPBUtil::checkString($var, True);
        $this->resource_name = $var;

        return $this;
    }

}
