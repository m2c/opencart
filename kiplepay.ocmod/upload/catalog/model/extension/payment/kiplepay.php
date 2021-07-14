<?php
class ModelExtensionPaymentKiplepay extends Model {
	const RECURRING_ACTIVE = 1;
    const RECURRING_INACTIVE = 2;
    const RECURRING_CANCELLED = 3;
    const RECURRING_SUSPENDED = 4;
    const RECURRING_EXPIRED = 5;
	const RECURRING_PENDING = 6;
	
	const TRANSACTION_DATE_ADDED = 0;
    const TRANSACTION_PAYMENT = 1;
    const TRANSACTION_OUTSTANDING_PAYMENT = 2;
    const TRANSACTION_SKIPPED = 3;
    const TRANSACTION_FAILED = 4;
    const TRANSACTION_CANCELLED = 5;
    const TRANSACTION_SUSPENDED = 6;
    const TRANSACTION_SUSPENDED_FAILED = 7;
    const TRANSACTION_OUTSTANDING_FAILED = 8;
    const TRANSACTION_EXPIRED = 9;

	public function getMethod($address, $total = false) {

		$this->load->language('extension/payment/kiplepay');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('payment_kiplepay_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		if ($this->config->get('payment_kiplepay_total') > 0 && $this->config->get('payment_kiplepay_total') > $total) {
			$status = false;
		} elseif (!$this->config->get('payment_kiplepay_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}

		$method_data = array();

		if ($status) {
			$method_data = array(
				'code'       => 'kiplepay',
				'title'      => $this->language->get('text_title'),
				'terms'      => '',
				'sort_order' => $this->config->get('payment_kiplepay_sort_order')
			);
		}

		return $method_data;
	  }
	  
	  public function recurringPayments() {
        return (bool)$this->config->get('payment_kiplepay_recurring_status');
    }

    public function createRecurring($recurring, $order_id, $description, $reference) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "order_recurring` SET `order_id` = '" . (int)$order_id . "', `date_added` = NOW(), `status` = '" . self::RECURRING_ACTIVE . "', `product_id` = '" . (int)$recurring['product_id'] . "', `product_name` = '" . $this->db->escape($recurring['name']) . "', `product_quantity` = '" . $this->db->escape($recurring['quantity']) . "', `recurring_id` = '" . (int)$recurring['recurring']['recurring_id'] . "', `recurring_name` = '" . $this->db->escape($recurring['recurring']['name']) . "', `recurring_description` = '" . $this->db->escape($description) . "', `recurring_frequency` = '" . $this->db->escape($recurring['recurring']['frequency']) . "', `recurring_cycle` = '" . (int)$recurring['recurring']['cycle'] . "', `recurring_duration` = '" . (int)$recurring['recurring']['duration'] . "', `recurring_price` = '" . (float)$recurring['recurring']['price'] . "', `trial` = '" . (int)$recurring['recurring']['trial'] . "', `trial_frequency` = '" . $this->db->escape($recurring['recurring']['trial_frequency']) . "', `trial_cycle` = '" . (int)$recurring['recurring']['trial_cycle'] . "', `trial_duration` = '" . (int)$recurring['recurring']['trial_duration'] . "', `trial_price` = '" . (float)$recurring['recurring']['trial_price'] . "', `reference` = '" . $this->db->escape($reference) . "'");

        return $this->db->getLastId();
	}
	public function updateRecurring($order_recurring_id,$order_id) {
        $this->db->query("UPDATE `" . DB_PREFIX . "order_recurring` SET reference='" . $order_id. "' WHERE order_recurring_id='" . (int)$order_recurring_id . "'");

        return true;
	}
	public function getRecurringTransaction($reference) {
        $recurring_sql = "SELECT * FROM `" . DB_PREFIX . "order_recurring_transaction` WHERE reference='" .$reference . "'";

        return $this->db->query($recurring_sql)->row;
    }
	public function addRecurringTransaction($order_recurring_id, $reference, $amount, $status) {
        if ($status) {
            $type = self::TRANSACTION_PAYMENT;
        } else {
            $type = self::TRANSACTION_FAILED;
        }

        $this->db->query("INSERT INTO `" . DB_PREFIX . "order_recurring_transaction` SET order_recurring_id='" . (int)$order_recurring_id . "', reference='" . $this->db->escape($reference) . "', type='" . (int)$type . "', amount='" . (float)$amount . "', date_added=NOW()");
    }

    public function updateRecurringExpired($order_recurring_id) {
        $recurring_info = $this->getRecurring($order_recurring_id);

        if ($recurring_info['trial']) {
            // If we are in trial, we need to check if the trial will end at some point
            $expirable = (bool)$recurring_info['trial_duration'];
        } else {
            // If we are not in trial, we need to check if the recurring will end at some point
            $expirable = (bool)$recurring_info['recurring_duration'];
        }

        // If recurring payment can expire (trial_duration > 0 AND recurring_duration > 0)
        if ($expirable) {
            $number_of_successful_payments = $this->getTotalSuccessfulPayments($order_recurring_id);

            $total_duration = (int)$recurring_info['trial_duration'] + (int)$recurring_info['recurring_duration'];
            
            // If successful payments exceed total_duration
            if ($number_of_successful_payments >= $total_duration) {
                $this->db->query("UPDATE `" . DB_PREFIX . "order_recurring` SET status='" . self::RECURRING_EXPIRED . "' WHERE order_recurring_id='" . (int)$order_recurring_id . "'");

                return true;
            }
        }

        return false;
    }

    public function updateRecurringTrial($order_recurring_id) {
        $recurring_info = $this->getRecurring($order_recurring_id);

        // If recurring payment is in trial and can expire (trial_duration > 0)
        if ($recurring_info['trial'] && $recurring_info['trial_duration']) {
            $number_of_successful_payments = $this->getTotalSuccessfulPayments($order_recurring_id);

            // If successful payments exceed trial_duration
            if ($number_of_successful_payments >= $recurring_info['trial_duration']) {
                $this->db->query("UPDATE `" . DB_PREFIX . "order_recurring` SET trial='0' WHERE order_recurring_id='" . (int)$order_recurring_id . "'");

                return true;
            }
        }

        return false;
    }

    public function suspendRecurringProfile($order_recurring_id) {
        $this->db->query("UPDATE `" . DB_PREFIX . "order_recurring` SET status='" . self::RECURRING_SUSPENDED . "' WHERE order_recurring_id='" . (int)$order_recurring_id . "'");

        return true;
    }

    private function getLastSuccessfulRecurringPaymentDate($order_recurring_id) {
        return $this->db->query("SELECT date_added FROM `" . DB_PREFIX . "order_recurring_transaction` WHERE order_recurring_id='" . (int)$order_recurring_id . "' AND type='" . self::TRANSACTION_PAYMENT . "' ORDER BY date_added DESC LIMIT 0,1")->row['date_added'];
    }

    private function getRecurring($reference) {
        $recurring_sql = "SELECT * FROM `" . DB_PREFIX . "order_recurring` WHERE reference='" .$reference . "'";

        return $this->db->query($recurring_sql)->row;
    }

    private function getTotalSuccessfulPayments($order_recurring_id) {
        return $this->db->query("SELECT COUNT(*) as total FROM `" . DB_PREFIX . "order_recurring_transaction` WHERE order_recurring_id='" . (int)$order_recurring_id . "' AND type='" . self::TRANSACTION_PAYMENT . "'")->row['total'];
    }

    private function paymentIsDue($order_recurring_id) {
        // We know the recurring profile is active.
        $recurring_info = $this->getRecurring($order_recurring_id);

        if ($recurring_info['trial']) {
            $frequency = $recurring_info['trial_frequency'];
            $cycle = (int)$recurring_info['trial_cycle'];
        } else {
            $frequency = $recurring_info['recurring_frequency'];
            $cycle = (int)$recurring_info['recurring_cycle'];
        }
        // Find date of last payment
        if (!$this->getTotalSuccessfulPayments($order_recurring_id)) {
            $previous_time = strtotime($recurring_info['date_added']);
        } else {
            $previous_time = strtotime($this->getLastSuccessfulRecurringPaymentDate($order_recurring_id));
        }

        switch ($frequency) {
            case 'day' : $time_interval = 24 * 3600; break;
            case 'week' : $time_interval = 7 * 24 * 3600; break;
            case 'semi_month' : $time_interval = 15 * 24 * 3600; break;
            case 'month' : $time_interval = 30 * 24 * 3600; break;
            case 'year' : $time_interval = 365 * 24 * 3600; break;
        }

        $due_date = date('Y-m-d', $previous_time + ($time_interval * $cycle));

        $this_date = date('Y-m-d');

        return $this_date >= $due_date;
    }

    public function setOrderStatus($order_id,$status_id) {
        
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$status_id . "' WHERE order_id = '" . (int)$order_id . "'");
		
		 return 'update_success';
	}
}
