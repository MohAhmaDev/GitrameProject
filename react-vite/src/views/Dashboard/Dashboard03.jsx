import React, { useRef } from 'react';
import { Box,
    Button,
} from '@mui/material';
import 'table2excel';
import { useReactToPrint } from "react-to-print";
import TableCreanceDettes from '../MUI/TableCreanceDettes';
import { useNavigate } from 'react-router-dom';



const Dashboard03 = () => {

    const navigate = useNavigate();
    const conponentPDF = useRef();

    const exportData = () => {
        const Table2Excel = window.Table2Excel;
        var table2excel = new Table2Excel({
            exclude:".noExl",
            defaultFileName:"Worksheet Name",
            filename:"SomeFile",
            fileext:".xls",
            preserveColors:true
        });
        table2excel.export(document.querySelectorAll("table"));	
    }

    const generatePDF= useReactToPrint({
        content: ()=>conponentPDF.current,
        documentTitle:"Userdata",
        onAfterPrint:()=>console.log("Data saved in PDF")
    });


    return (
        <div style={{ background: "#fff", padding: "20px", borderRadius: "5px" }}>
            <TableCreanceDettes/>
            <Box display="flex" justifyContent="end" mt="20px" m="25px">
                <Button style={{ marginLeft: "10px" }} variant="contained"
                onClick={() => navigate('/report/dette_creances')} 
                color="error"> PDF REPORT </Button> 
                {/*  / onClick={ev => {exportData()}}>*/}
                <Button style={{ marginLeft: "10px" }} variant="contained" 
                onClick={ev => {exportData()}} color='success'>EXEL REPORT </Button>                  
            </Box>
        </div>
    );


};

export default Dashboard03;
