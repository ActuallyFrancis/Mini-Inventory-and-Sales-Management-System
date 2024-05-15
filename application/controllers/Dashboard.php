<?php
defined('BASEPATH') or exit('');

/**
 * Description of Dashboard
 *
 * @author Amir <amirsanni@gmail.com>
 * @date 31st Dec, 2015
 */
class Dashboard extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();

    $this->genlib->checkLogin();

    $this->load->model(['item', 'transaction', 'analytic']);
  }


  /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */

  /**
   * 
   */
  public function index()
  {
    $data['topDemanded'] = $this->analytic->topDemanded();
    $data['leastDemanded'] = $this->analytic->leastDemanded();
    $data['highestEarners'] = $this->analytic->highestEarners();
    $data['lowestEarners'] = $this->analytic->lowestEarners();
    $data['totalItems'] = $this->db->count_all('items');
    $data['totalSalesToday'] = (int)$this->analytic->totalSalesToday();
    $data['totalTransactions'] = $this->transaction->totalTransactions();
    $data['dailyTransactions'] = $this->analytic->getDailyTrans();
    $data['transByDays'] = $this->analytic->getTransByDays();
    $data['transByMonths'] = $this->analytic->getTransByMonths();
    $data['transByYears'] = $this->analytic->getTransByYears();

    $values['pageContent'] = $this->load->view('dashboard', $data, TRUE);

    $values['pageTitle'] = "Dashboard";

    $this->load->view('main', $values);
  }

  /*
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    ********************************************************************************************************************************
    */

  /**
   * 
   * @param type $year year of earnings to fetch
   * @param boolean $not_ajax if request if ajax request or not
   * @return int
   */
  public function earningsGraph($year = "", $not_ajax = false)
  {
    //set the year of expenses to show
    $year_to_fetch = $year ? $year : date('Y');

    $earnings = $this->genmod->getYearEarnings($year_to_fetch);
    $lastEarnings = 0;
    $monthEarnings = array();
    $hightEarn['highestEarning'] = 0;
    $dataarr = [];
    $allMonths = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

    if ($earnings) {
      foreach ($allMonths as $allMonth) {
        foreach ($earnings as $get) {
          $earningMonth = date("M", strtotime($get->transDate));

          if ($allMonth == $earningMonth) {
            $lastEarnings += $get->totalPrice;

            $monthEarnings[$allMonth] = $lastEarnings;
          } else {
            if (!array_key_exists($allMonth, $monthEarnings)) {
              $monthEarnings[$allMonth] = 0;
            }
          }
        }

        if ($lastEarnings > $hightEarn['highestEarning']) {
          $hightEarn['highestEarning'] = $lastEarnings;
        }

        $lastEarnings = 0;
      }

      foreach ($monthEarnings as $me) {
        $dataarr[] = $me;
      }
    } else { //if no earning, set earning to 0
      foreach ($allMonths as $allMonth) {
        $dataarr[] = 0;
      }
    }

    //add info into array
    $json = array("total_earnings" => $dataarr, 'earningsYear' => $year_to_fetch);

    //set final output based on where the request is coming from
    if ($not_ajax) {
      return $json;
    } else {
      $this->output->set_content_type('application/json')->set_output(json_encode($json));
    }
  }
}
