import React from 'react';

 // Tell webpack that Button.js uses these styles

const GitramReports = () => {
    return (
        <div class="container bootstrap snippets bootdeys">
		<div class="row">
			<div class="col-sm-12">
				<div class="panel panel-default invoice" id="invoice">
					<div class="panel-body">
						<div class="invoice-ribbon">
							<div class="ribbon-inner">GITRAMA</div>
						</div>
						<div class="row">
							<div class="col-sm-6 top-left">
								<img src="Gitrama.png" width="100" height="100" alt=""/>
							</div>
							<div class="col-sm-6 top-right">
								<h3 class="marginright" 
								style={{marginRight: "50px"}}>GROUPE D'INFRASTRUCTURES DE TRAVAUX MARITIMES </h3>
								<span class="marginright">14 April 2014</span>
							</div>
						</div>
						<hr/>
						<div class="row">
							<div class="col-xs-4 from">
								<p class="lead marginbottom">From : Dynofy</p>
								<p>350 Rhode Island Street</p>
								<p>Suite 240, San Francisco</p>
								<p>California, 94103</p>
								<p>Phone: 415-767-3600</p>
								<p>Email: <a href="/cdn-cgi/l/email-protection" class="__cf_email__"
									data-cfemail="74171b1a0015170034100d1a1b120d5a171b19">[email&#160;protected]</a></p>
							</div>
						</div>
						<div class="row table-row">
							<table class="table table-striped">
								<thead>
									<tr>
										<th class="text-center" style={{width:"5%"}}>#</th>
										<th style={{width:"50%"}}>Item</th>
										<th class="text-right" style={{width:"15%"}}>Quantity</th>
										<th class="text-right" style={{width:"15%"}}>Unit Price</th>
										<th class="text-right" style={{width:"15%"}}>Total Price</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td class="text-center">1</td>
										<td>Flatter Theme</td>
										<td class="text-right">10</td>
										<td class="text-right">$18</td>
										<td class="text-right">$180</td>
									</tr>
									<tr>
										<td class="text-center">2</td>
										<td>Flat Icons</td>
										<td class="text-right">6</td>
										<td class="text-right">$59</td>
										<td class="text-right">$254</td>
									</tr>
									<tr>
										<td class="text-center">3</td>
										<td>Wordpress version</td>
										<td class="text-right">4</td>
										<td class="text-right">$95</td>
										<td class="text-right">$285</td>
									</tr>
									<tr class="last-row">
										<td class="text-center">4</td>
										<td>Server Deployment</td>
										<td class="text-right">1</td>
										<td class="text-right">$300</td>
										<td class="text-right">$300</td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="row">
							<div class="col-xs-6 margintop">
								<p class="lead marginbottom">THANK YOU!</p>
								<button class="btn btn-success" id="invoice-print"><i class="fa fa-print"></i> Print
									Invoice</button>
								<button class="btn btn-danger"><i class="fa fa-envelope-o"></i> Mail Invoice</button>
							</div>
							<div class="col-xs-6 text-right pull-right invoice-total">
								<p>Subtotal : $1019</p>
								<p>Discount (10%) : $101 </p>
								<p>VAT (8%) : $73 </p>
								<p>Total : $991 </p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
    );
};

export default GitramReports;