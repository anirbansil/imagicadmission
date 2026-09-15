<table style="background: #d7e5ff;">
    <tr>
        <td>
            <table style="background :#D8E4BC;">
                <thead>
                
                <tr>
                    <th style="height: 25px;margin: 0 10px;width: 25%;"><b>Serial No</b></th>
                    <th style="height: 25px;margin: 0 10px;width: 25%;"><b>Student Name</b></th>
                    <th style="height: 25px;margin: 0 10px;width: 25%;"><b>Phone Number</b></th>
                    <th style="height: 25px;margin: 0 10px;width: 25%;"><b>Centre Name</b></th>
                    <th style="height: 25px;margin: 0 10px;width: 25%;"><b>Course Name</b></th>
                    <th style="height: 25px;margin: 0 10px;width: 25%;"><b>Date of Inquiry</b></th>
                </tr>
                </thead>
                <tbody>
                    @php $sl_no=1; @endphp
                    @forelse($inquires as $inquire)
                      <tr> 
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{ @$inquire->serial_no }}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{ @$inquire->first_name }} {{ @$inquire->mid_name }} {{ @$inquire->last_name }}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{ @$inquire->mobile_no }}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{ @$inquire->center_name ? App\Helpers\Helper::getCenterName(@$inquire->center_name) : '' }}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{ @$inquire->course ? App\Helpers\Helper::getCourseName(@$inquire->course) : '' }}</td>
                        <td style="height: 25px;margin: 0 10px;width: 25%;">{{ date('d M Y', strtotime(@$inquire->created_at)) }}</td>
                    
                    @empty                  
                    @endforelse

                </tbody>
            </table>
            

        </td>
    </tr>
</table>
