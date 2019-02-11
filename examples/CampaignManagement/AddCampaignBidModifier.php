<?php
/**
 * Copyright 2018 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Google\Ads\GoogleAds\Examples\CampaignManagement;

require __DIR__ . '/../../vendor/autoload.php';

use GetOpt\GetOpt;
use Google\Ads\GoogleAds\Examples\Utils\ArgumentNames;
use Google\Ads\GoogleAds\Examples\Utils\ArgumentParser;
use Google\Ads\GoogleAds\Lib\GoogleAdsClient;
use Google\Ads\GoogleAds\Lib\GoogleAdsClientBuilder;
use Google\Ads\GoogleAds\Lib\GoogleAdsException;
use Google\Ads\GoogleAds\Lib\OAuth2TokenBuilder;
use Google\Ads\GoogleAds\Util\ResourceNames;
use Google\Ads\GoogleAds\V0\Common\InteractionTypeInfo;
use Google\Ads\GoogleAds\V0\Enums\InteractionTypeEnum;
use Google\Ads\GoogleAds\V0\Errors\GoogleAdsError;
use Google\Ads\GoogleAds\V0\Resources\CampaignBidModifier;
use Google\Ads\GoogleAds\V0\Services\CampaignBidModifierOperation;
use Google\ApiCore\ApiException;
use Google\Protobuf\DoubleValue;
use Google\Protobuf\StringValue;

/**
 * This example demonstrates how to add a campaign-level bid modifier for call interactions.
 */
class AddCampaignBidModifier
{
    const CUSTOMER_ID = 'INSERT_CUSTOMER_ID_HERE';
    const CAMPAIGN_ID = 'INSERT_CAMPAIGN_ID_HERE';
    const BID_MODIFIER_VALUE = 'INSERT_BID_MODIFIER_VALUE_HERE';

    public static function main()
    {
        // Either pass the required parameters for this example on the command line, or insert them
        // into the constants above.
        $options = (new ArgumentParser())->parseCommandArguments([
            ArgumentNames::CUSTOMER_ID => GetOpt::REQUIRED_ARGUMENT,
            ArgumentNames::CAMPAIGN_ID => GetOpt::REQUIRED_ARGUMENT,
            ArgumentNames::BID_MODIFIER_VALUE => GetOpt::REQUIRED_ARGUMENT
        ]);

        // Generate a refreshable OAuth2 credential for authentication.
        $oAuth2Credential = (new OAuth2TokenBuilder())->fromFile()->build();

        // Construct a Google Ads client configured from a properties file and the
        // OAuth2 credentials above.
        $googleAdsClient = (new GoogleAdsClientBuilder())->fromFile()
            ->withOAuth2Credential($oAuth2Credential)
            ->build();

        try {
            self::runExample(
                $googleAdsClient,
                $options[ArgumentNames::CUSTOMER_ID] ?: self::CUSTOMER_ID,
                $options[ArgumentNames::CAMPAIGN_ID] ?: self::CAMPAIGN_ID,
                $options[ArgumentNames::BID_MODIFIER_VALUE] ?: self::BID_MODIFIER_VALUE
            );
        } catch (GoogleAdsException $googleAdsException) {
            printf(
                "Request with ID '%s' has failed.%sGoogle Ads failure details:%s",
                $googleAdsException->getRequestId(),
                PHP_EOL,
                PHP_EOL
            );
            foreach ($googleAdsException->getGoogleAdsFailure()->getErrors() as $error) {
                /** @var GoogleAdsError $error */
                printf(
                    "\t%s: %s%s",
                    $error->getErrorCode()->getErrorCode(),
                    $error->getMessage(),
                    PHP_EOL
                );
            }
        } catch (ApiException $apiException) {
            printf(
                "ApiException was thrown with message '%s'.%s",
                $apiException->getMessage(),
                PHP_EOL
            );
        }
    }

    /**
     * Runs the example.
     *
     * @param GoogleAdsClient $googleAdsClient the Google Ads API client
     * @param int $customerId the client customer ID without hyphens
     * @param int $campaignId the ID of the campaign where the bid modifier will be added
     * @param float $bidModifierValue the value of the bid modifier to add
     */
    public static function runExample(
        GoogleAdsClient $googleAdsClient,
        $customerId,
        $campaignId,
        $bidModifierValue
    ) {
        // Creates a campaign bid modifier.
        $campaignBidModifier = new CampaignBidModifier([
            'campaign' => new StringValue(
                ['value' => ResourceNames::forCampaign($customerId, $campaignId)]
            ),
            // Use the specified bid modifier value.
            'bid_modifier' => new DoubleValue(['value' => $bidModifierValue]),
            // Make the bid modifier apply to call interactions.
            'interaction_type' => new InteractionTypeInfo(
                ['type' => InteractionTypeEnum\InteractionType::CALLS]
            )
        ]);

        // Creates a campaign bid modifier operation for creating a campaign bid modifier.
        $campaignBidModifierOperation = new CampaignBidModifierOperation();
        $campaignBidModifierOperation->setCreate($campaignBidModifier);

        // Issues a mutate request to add the campaign bid modifier.
        $campaignBidModifierServiceClient = $googleAdsClient->getCampaignBidModifierServiceClient();
        $response = $campaignBidModifierServiceClient->mutateCampaignBidModifiers(
            $customerId,
            [$campaignBidModifierOperation]
        );

        printf("Added %d campaign bid modifier:%s", $response->getResults()->count(), PHP_EOL);

        foreach ($response->getResults() as $addedCampaignBidModifier) {
            /** @var CampaignBidModifier $addedCampaignBidModifier */
            print $addedCampaignBidModifier->getResourceName() . PHP_EOL;
        }
    }
}

AddCampaignBidModifier::main();