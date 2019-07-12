<?php
/*Class Customer{
    function  __construct($cus_id,$gwid,$firs_name,$last_name,$DoB,$insurance,$addr_street,$addr_apt,$addr_state,$zipcode,$phone)
    {$this->cus_id = $cus_id;
    $this->gwid = $gwid;
    $this->first_name = $firs_name;
    $this->last_name = $last_name;
    $this ->DoB =$DoB;
    $this ->insurance = $insurance;
    $this->addr_street = $addr_street;
    $this ->addr_apt = $addr_apt;
    $this ->addr_state = $addr_state;
    $this ->zipcode = $zipcode;
    $this ->phone = $phone;
    $this->order = [];
    }
    function make_order($order_id,$cus_id,$rx_mu,$orderGenDate){

        $order_obj = new Order($order_id,$cus_id,$rx_mu,$orderGenDate);


    }
}

class Order{
    static $ordercount = 0;
    function __construct($order_id,$cusid,$rx_num,$gen_datedate){
        $this->cus_id = $cusid;
        $this->rx_num = $rx_num;
        $this ->gen_date = $$gen_datedate;
        self ::$ordercount++;
        self ::$ordercount = $order_id;
    }

    function tell_order(){}

    }
*/
Class Drug{
    function __construct($NDC, $drug_name, $drug_stren, $production_date, $discard_date, $unit_price){
        $this->NDC = $NDC;
        $this->drug_name = $drug_name;
        $this->drug_stren = $drug_stren;
        $this->production_date = $production_date;
        $this->discard_date = $discard_date;
        $this->unit_price = $unit_price;
    } }

Class Prescription {
    function __construct($rx_num, $cusid,  $rx_written_date,$prescriber_id){
        $this ->rx_num = $rx_num;
        $this ->cusid = $cusid;
        $this ->prescriber_id = $prescriber_id;
       # $this ->doc_lastn = $doc_lastn;
        #$this ->doc_firstn = $doc_firstn;
        $this ->rx_written_date = $rx_written_date;
        $this ->drugs = [];

    }



    function create_drug($db, $cusid)
    {
        $sql = "SELECT P.rx_num, P.rx_written_date, PD.refill, P.cus_id,D.NDC,D.drug_name,
	        D.drug_stren,D.production_date,D.discard_date, D.unit_price FROM Prescription P
            Join  Prescription_drug PD ON P.rx_num = PD.rx_num
            JOIN Drug D ON PD.NDC = D.NDC
            where P.cus_id =  $cusid AND P.rx_num = '" . $this->rx_num . "'";
        $stmt = $db->query($sql);

        while ($row = $stmt->fetch()) {
            $NDC = $row['NDC'];
            $drug_name = $row['drug_name'];
            $drug_stren = $row['drug_stren'];
            $production_date = $row['production_date'];
            $discard_date = $row['discard_date'];
            $unit_price = $row['unit_price'];

            $drug_obj = new Drug($NDC, $drug_name, $drug_stren, $production_date, $discard_date, $unit_price);

            $this->drugs[]=$drug_obj;
        }
    }
}

