<?php
/**
 * Copyright 2014 Facebook, Inc.
 *
 * You are hereby granted a non-exclusive, worldwide, royalty-free license to
 * use, copy, modify, and distribute this software in source code or binary
 * form for use in connection with the web services and APIs provided by
 * Facebook.
 *
 * As with any software that integrates with the Facebook platform, your use
 * of this software is subject to the Facebook Developer Principles and
 * Policies [http://developers.facebook.com/policy/]. This copyright notice
 * shall be included in all copies or substantial portions of the software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 */

namespace FacebookAdsTest\Object;

use FacebookAds\Object\AdCampaign;
use FacebookAds\Object\AdSet;
use FacebookAds\Object\Fields\AdCampaignFields;
use FacebookAds\Object\Fields\AdSetFields;
use FacebookAds\Object\Fields\TargetingSpecsFields;
use FacebookAds\Object\TargetingSpecs;
use FacebookAds\Object\Values\BillingEvents;
use FacebookAds\Object\Values\OptimizationGoals;

class AdSetTest extends AbstractCrudObjectTestCase {

  /**
   * @var AdCampaign
   */
  protected $adCampaign;

  public function setup() {
    parent::setup();
    $this->adCampaign = new AdCampaign(null, $this->getConfig()->accountId);
    $this->adCampaign->{AdCampaignFields::NAME}
      = $this->getConfig()->testRunId;
    $this->adCampaign->create();
  }

  public function tearDown() {
    $this->adCampaign->delete();
    $this->adCampaign = null;
    parent::tearDown();
  }

  public function testCrud() {
    $targeting = new TargetingSpecs();
    $targeting->{TargetingSpecsFields::GEO_LOCATIONS}
      = array('countries' => array('US'));

    $set = new AdSet(null, $this->getConfig()->accountId);
    $set->{AdSetFields::CAMPAIGN_GROUP_ID}
      = $this->adCampaign->{AdCampaignFields::ID};
    $set->{AdSetFields::NAME} = $this->getConfig()->testRunId;
    $set->{AdSetFields::CAMPAIGN_STATUS} = AdSet::STATUS_PAUSED;
    $set->{AdSetFields::OPTIMIZATION_GOAL} = OptimizationGoals::REACH;
    $set->{AdSetFields::BILLING_EVENT} = BillingEvents::IMPRESSIONS;
    $set->{AdSetFields::BID_AMOUNT} = 2;
    $set->{AdSetFields::DAILY_BUDGET} = '150';
    $set->{AdSetFields::TARGETING} = $targeting;
    $set->{AdSetFields::START_TIME}
      = (new \DateTime("+1 week"))->format(\DateTime::ISO8601);
    $set->{AdSetFields::END_TIME}
      = (new \DateTime("+2 week"))->format(\DateTime::ISO8601);

    $this->assertCanCreate($set);
    $this->assertCanRead($set);
    $this->assertCanUpdate($set, array(
      AdSetFields::NAME => $this->getConfig()->testRunId.' updated',
    ));
    $this->assertCanFetchConnection($set, 'getAdGroups');
    $this->assertCanFetchConnection($set, 'getAdCreatives');
    $this->assertCanFetchConnection($set, 'getInsights');
    $this->assertCanFetchConnection($set, 'getInsightsAsync');

    $this->assertCanBeLabeled($set);
    $this->assertCanArchive($set);

    $this->assertCanDelete($set);
  }
}
