<table>
    <thead>
        <tr>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #000000; color: #ffffff;">No</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #000000; color: #ffffff;">Transaction ID</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #000000; color: #ffffff;">Transfer Type</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #000000; color: #ffffff;">Beneficiary ID</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #00a651; color: #ffffff;">Credited Account</th>
            <th style="font-weight: bold; text-align: left; border: 1px solid #000000; background-color: #00a651; color: #ffffff;">Receiver Name</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #00a651; color: #ffffff;">Amount</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #000000; color: #ffffff;">NIP</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #00a651; color: #ffffff;">Remark</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #000000; color: #ffffff;">Beneficiary email address</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #000000; color: #ffffff;">Receiver Swift Code</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #000000; color: #ffffff;">Receiver Cust Type</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000; background-color: #000000; color: #ffffff;">Receiver Cust Residence</th>
        </tr>
    </thead>
    <tbody>
        @php
            $no = 1;
        @endphp
        @foreach($rows as $row)
            <tr>
                <td style="text-align: center; border: 1px solid #e2e8f0;">{{ $no++ }}</td>
                <td style="text-align: center; border: 1px solid #e2e8f0;"></td>
                <td style="text-align: center; border: 1px solid #e2e8f0;">{{ strtoupper($row['transfer_type'] ?: 'BCA') }}</td>
                <td style="text-align: center; border: 1px solid #e2e8f0;"></td>
                <!-- Force credited account to be treated as string in Excel to avoid scientific notation/truncation -->
                <td style="text-align: center; border: 1px solid #e2e8f0; background-color: #f0fdf4;">{{ $row['credited_account'] }}</td>
                <td style="text-align: left; border: 1px solid #e2e8f0; background-color: #f0fdf4; font-weight: bold;">{{ $row['receiver_name'] }}</td>
                <td style="text-align: center; border: 1px solid #e2e8f0; background-color: #f0fdf4; font-weight: bold;">{{ number_format($row['amount'], 0, ',', '.') }}</td>
                <td style="text-align: center; border: 1px solid #e2e8f0;"></td>
                <td style="text-align: center; border: 1px solid #e2e8f0; background-color: #f0fdf4;">{{ $row['remark'] }}</td>
                <td style="text-align: center; border: 1px solid #e2e8f0;"></td>
                <td style="text-align: center; border: 1px solid #e2e8f0;"></td>
                <td style="text-align: center; border: 1px solid #e2e8f0;"></td>
                <td style="text-align: center; border: 1px solid #e2e8f0;"></td>
            </tr>
        @endforeach
    </tbody>
</table>
