<?php
namespace Test\Tasks;


use Test\Models\Foodlicense;
use Test\Models\Qs;
use Test\Models\Sc;

class FoodTask extends \Phalcon\CLI\Task
{
    /**
     * @debug php cli.php food sc
     */
    public function scAction()
    {
        $scs = Sc::find()->toArray();

        foreach ($scs as $key => $sc) {
            $insert['Type'] = 'SC';
            $insert['CompanyName'] = $sc['producerName'];
            $insert['SocialCode'] = $sc['socialCreditCode'];
            $insert['Boss'] = $sc['legalRepresentative'];
            $insert['Address'] = $sc['residence'];
            $insert['ProduceAddress'] = $sc['productionAddress'];
            $insert['FoodCat'] = $sc['foodCategories'];
            $insert['License'] = str_replace(' ', '', $sc['licenseNumber']);
            $insert['ManagementOrganization'] = $sc['managementOrganization'];
            $insert['ManagementPerson'] = $sc['managementManagemen'];
            $insert['IssueUnit'] = $sc['issuingAuthority'];
            $insert['Issue'] = $sc['Issuer'];
            $insert['IssueDate'] = date('Y-m-d', strtotime($sc['issueDate']));
            $insert['ExpireDate'] = date('Y-m-d', strtotime($sc['validUntil']));
            $insert['LicenseDetail'] = $sc['licenseDetails'];

            $foodLicense = new Foodlicense();

            if ($foodLicense->create($insert) == false) {
                foreach ($foodLicense->getMessages() as $message) {
                    echo $message, "\n";
                }
            } else {
                echo 'InsertID.'. $foodLicense->id;echo PHP_EOL;
            }
        }
    }

    /**
     * @debug php cli.php food qs
     */
    public function qsAction()
    {
        $qss = Qs::find()->toArray();

        foreach ($qss as $key => $qs) {
            $insert['Type'] = 'QS';
            $insert['CompanyName'] = $qs['companyName'];
            $insert['ProductName'] = $qs['productName'];
            $insert['Address'] = $qs['residence'];
            $insert['ProduceAddress'] = $qs['productionAddress'];
            $insert['License'] = str_replace(' ', '', $qs['certificateNumber']);
            $insert['IssueUnit'] = $qs['issueUnit'];
            $insert['IssueDate'] = date('Y-m-d', strtotime($qs['issuingDate']));
            $insert['ExpireDate'] = date('Y-m-d', strtotime($qs['expiryDate']));
            $insert['ExamineMethod'] = $qs['inspectionMethod'];

            $foodLicense = new Foodlicense();

            if ($foodLicense->create($insert) == false) {
                foreach ($foodLicense->getMessages() as $message) {
                    echo $message, "\n";
                }
            } else {
                echo 'InsertID.'. $foodLicense->id;echo PHP_EOL;
            }
        }
    }
}