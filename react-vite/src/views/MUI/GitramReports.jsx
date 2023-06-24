import React, { useRef } from 'react';
import { Helmet } from 'react-helmet';
import TemplateTest from '../TableRHS';
import { useReactToPrint } from "react-to-print";
import { Box, Button } from '@mui/material';


const GitramReports = () => {

    const conponentPDF = useRef();
    const date = new Date().toLocaleDateString("de-DE");



    const generatePDF= useReactToPrint({
        content: ()=>conponentPDF.current,
        documentTitle:"Userdata",
        onAfterPrint:()=>alert("Data saved in PDF")
    });

    return (
        <>
            <div class="invoice-box" ref={conponentPDF}>
            <table cellpadding="0" cellspacing="0">
                <tr class="top">
                    <td colspan="2">
                        <table>
                            <tr>
                                <td class="title">
                                    <img src="Gitrama.png" style={{widows: "100%", maxWidth: "150px"}} />
                                </td>

                                <td>
                                    <h1 style={{ color: "#3ba0ff" }}>GROUPE D'INFRASTRUCTURES DE <br /> TRAVAUX MARITIMES  </h1><br />
                                    <b> Date: {date}</b> <br />
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr class="information">
                    <td colspan="2">
                        <table>
                            <tr>
                                <td>
                                    Voie C,<br />
                                    Zone industrielle Reghaia,<br />
                                    Alger
                                </td>

                                <td>
                                    tel : +(00) 213 864 060  .<br />
                                    fax : + (00) 213 864 061<br />
                                    contact@gitrama.dz
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            {<TemplateTest hide={true}/>}
            </div>
            <Box sx={{ marginTop: "25px", marginLeft: "125px" }}>
                <Button style={{ marginLeft: "10px" }} onClick={generatePDF} color="error"> PDF REPORT </Button> 
            </Box>
        </>
    );
};

export default GitramReports;