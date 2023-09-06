<?
include("../cms/php/dbconn.php");
require 'vendor/autoload.php';

use SparkPost\SparkPost;
use GuzzleHttp\Client;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;

$httpClient = new GuzzleAdapter(new Client());
$sparky = new SparkPost($httpClient, ['key'=>SPARK_POST_KEY]);

$sparky->setOptions(['async' => false]);
				try
				{
					//SparkPost Init
					$httpClient = new GuzzleAdapter(new Client());
					$sparky = new SparkPost($httpClient, ['key'=>SPARK_POST_KEY]);
					//SparkPost Init
					$sparky->setOptions(['async' => false]);
					$promise = $sparky->transmissions->post([
						'content' => ['template_id' => 'refund-donation'],
						'substitution_data' => [
							"sHOMECMS" => sHOMECMS,
							"sHOME" => sHOMECMS,
							"ufname" => "Saadat",
							"ulname" => "Ansari",
							"transaction_date" => "11/28/2016 08:01:00",
							"actualdonationamount" => "200.00",
							"donationamount" => "100.00",
							"payment_through" => "Visa",
							"card_number" => "4242",
							"participantfname" => "Kurt",
							"participantlname" => "Gairing",
							"camptitle" => "River High School Baseball 2016"
						],
						'campaign_id' => "Saadat Ansari requested refund",
						'metadata' => [
							'Campaign_ID' => "1",
							'Campaign_Name' => "River High School Baseball 2016",
							'Subject' => "Saadat Ansari requested refund"
						],
						'recipients' => [
							[
								'address' => [
									'name' => "Saadat Ansari",
									'email' => "write4saadat@hotmail.com",
								],
							],
						],
						'cc' => [
							[
								'address' => [
									'name' => sWEBSITENAME." Administrator",
									'email' => INFO_EMAIL,
								],
							],
						],
					]);
				} catch (\Exception $e) {
					echo $e->getCode()."\n";
					echo $e->getMessage()."\n";
				}

?>