import React, { useEffect, useRef, useState } from 'react';
import DenseTable from '../MUI/DenseTable';
import { Box, TextField, Button, CircularProgress,
FormControl, Select, InputLabel, MenuItem} from '@mui/material';
import axiosClient from '../../axios-client';
import { useForm, Controller } from "react-hook-form";
import { useDisplayContext } from '../../contexts/DisplayContext';
import { Link } from 'react-router-dom';
import 'table2excel';
import { useReactToPrint } from "react-to-print";


const Dashboard02 = () => {

    const conponentPDF= useRef();

    const [data, setData] = useState({
        filiale: "",
        date: ""
    });
    const {filiales, getFiliales, setFiliales} = useDisplayContext()
    const [dash, setDash] = useState({});
    const [kpi, setKpi] = useState({});
    const [rows, setRows] = useState({});
    const [dates, setDates] = useState({});

    const getFinanceDashboard = (req) => {
        axiosClient.post('/finance_dashboard', req).then(({data}) => {
            setDash(data.tab1);
            setKpi(data.tab2);
        }).catch((err) => {
            console.log(err)
        })
    }

    const getDates = () => {
        axiosClient.get('/finance_years').then(({data}) => {
            setDates(data)
        }).catch((err) => {
            console.log(err)
        })
    }

    useEffect(() => {
        getDates()
        getFiliales();
        getFinanceDashboard(data);
        console.log('load', data);
    }, [data])


    function createData(calcule_Agregats, Montant_Realisation, Montant_Privision, Ecart_Valeur, taux_Realisation) {
        return { calcule_Agregats, Montant_Realisation, Montant_Privision, Ecart_Valeur, taux_Realisation };
    }

    useEffect(() => {
        if (Object.keys(dash).length !== 0) {
            const rows = dash.map(resultat => {
                const Agregat_calculer = resultat.Agregat_calculer;
                const Montant_Realisation = resultat.Montant_Realisation;
                const Montant_Privision = resultat.Montant_Privision;
                const Ecart_Valeur = resultat.Ecart_Valeur;
                const taux_Realisation = resultat.taux;
        
                return createData(Agregat_calculer, Montant_Realisation, Montant_Privision, Ecart_Valeur, taux_Realisation);
            });
            console.log("rows", rows) 
            setRows(rows)         
        }
    }, [dash])

    const { handleSubmit, control, 
        register, getValues, watch , setValue, reset, formState, setError, formState: { errors } } = useForm({
        mode: "onChange"
    });

    const onSubmit = () => {
        setData({
            filiale: "",
            date: ""
        })
    }

    const downloadReport = () => {
        const invoice = document.getElementById("print");
        console.log(invoice);
        var opt = {
            margin: 1,
            filename: 'myfile.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
        };
        html2pdf().from(invoice).set(opt).save();
    }

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
        console.log("ou ! oui vous l'avais")
    }

    const generatePDF= useReactToPrint({
        content: ()=>conponentPDF.current,
        documentTitle:"Userdata",
        onAfterPrint:()=>alert("Data saved in PDF")
    });

    console.log(kpi)

    return (
        <React.Fragment>
            <h1> Dashboard Finance </h1>
            <div className="card animated fadeInDown">
                <h2 style={{ gridColumn: "span 6" }}> Filtres </h2>
                <form onSubmit={handleSubmit(onSubmit)}>
                <Box m="25px" display="grid" gridTemplateColumns="repeat(6, minmax(0, 1fr))" gap="30px" > 
                    <label style={{ gridColumn: "span 1" }}> Finance : (filitre filiale) </label>
                    <Controller
                        control={control}
                        name="filiale"
                        render={({ field: { onChange } }) => (
                            <FormControl variant="outlined" sx={{ width: "300px" }}>
                            <InputLabel id="demo-simple-select-label"> filiale  </InputLabel>
                            <Select
                                sx={{ gridColumn: "span 2" }}
                                label="filiale"
                                {...register("filiale")}
                                value={data.filiale}
                                onChange={(ev) => setData({...data, filiale: ev.target.value})}
                            >
                                {(Object.keys(filiales).length !== 0) && filiales?.map(filiale => (
                                <MenuItem value={filiale?.id} key={filiale?.id}> {filiale?.name} </MenuItem>
                                ))}
                            </Select>
                            </FormControl>
                        )}
                    /> 
                </Box> 
                <Box m="25px" display="grid" gridTemplateColumns="repeat(6, minmax(0, 1fr))" gap="30px" 
                > 
                    <label style={{ gridColumn: "span 1" }}> Finance : (filitre Annee) </label>
                    <Controller
                        control={control}
                        name="date"
                        render={({ field: { onChange } }) => (
                            <FormControl variant="outlined" sx={{ width: "300px" }}>
                            <InputLabel> annee  </InputLabel>
                            <Select
                                sx={{ gridColumn: "span 2" }}
                                label="annee"
                                {...register("date")}
                                value={data.date}
                                onChange={(ev) => setData({...data, date: ev.target.value})}
                            >
                                {(Object.keys(dates).length !== 0) && dates?.map(date => (
                                <MenuItem value={date?.year} > {date?.year} </MenuItem>
                                ))}
                            </Select>
                            </FormControl>
                        )}
                    /> 
                </Box> 

                
                {(rows && Object.keys(rows).length !== 0) ? 
                <Box ref={conponentPDF}>
                    <h2 style={{ marginBottom: "10px", textAlign: "center" }}> Agrégat Finance Cumuler </h2>
                    <DenseTable data={rows} id="print"/>
                </Box>
                 : <CircularProgress disableShrink />}
                <Box display="flex" justifyContent="end" mt="20px" m="10px">
                    <Button type="submit" color="primary" variant="contained">
                        Renaitialiser
                    </Button>
                    
                </Box>                                                        
                </form>    
                {/* <Button style={{ marginLeft: "10px" }} onClick={ev => {downloadReport()}} color="error"> PDF REPORT </Button>  */}
                <Button style={{ marginLeft: "10px" }} onClick={ generatePDF} color="error"> PDF REPORT </Button> 
                <Button style={{ marginLeft: "10px" }} onClick={ev => {exportData()}} color='success'>EXEL REPORT </Button>                  
                {/* <div className="io">
                    <div className="row">
                        <div className="col-md-12">
                        <h5 className="mt-2">User List</h5> 
                        
                        <div className="d-grid d-md-flex justify-content-md-end mb-3">
                        </div> 
                    <div ref={conponentPDF} style={{width:'100%'}}>
                        <table className="table table-bordered" >
                            <thead className="bg-light">
                            <tr>
                                <th>Sr. No</th>
                                <th>Name</th>
                                <th>User Name</th>
                                <th>Email</th>
                                <th>Phone No</th>
                                <th>Gender</th>
                                <th>Country</th>
                                <th>State</th>
                                <th>Address1</th>
                                <th>Action</th>
                                </tr> 
                            </thead>
                            <tbody>
                                <tr>
                                    <td>+</td>
                                    <td>name</td>
                                    <td>username</td>
                                    <td>email</td>
                                    <td>phoneno</td>
                                    <td>gender</td>
                                    <td>countryname</td>
                                    <td>state_name</td>
                                    <td>address1</td>
                                    <td>
                                        Edit
                                    </td>
                                </tr>
                            </tbody>                        
                        </table>         
                        </div>
                        <div className="d-grid d-md-flex justify-content-md-end mb-3">
                        <button className="btn btn-success" onClick={ generatePDF}>PDF</button>                       
                        </div> 
                        </div>
                    </div>
                </div>   */}
                {
                    (kpi && Object.keys(kpi).length !== 0) ? 
                    <div style={{ marginTop: "50px" }}>
                        <h2 style={{ marginBottom: "10px", textAlign: "center" }}> Agrégat Finance Cumuler </h2>
                        <h2></h2>
                        <table>
                        <thead>
                            <tr>
                            {(Object.keys(kpi).length !== 0) && kpi.map(data => (
                                    <th value={data?.label} > {data?.label} </th>
                            ))}    
                            </tr> 
                        </thead>
                        <tbody>
                            <tr>
                            {(Object.keys(kpi).length !== 0) && kpi.map(data => (
                                    <td value={data?.val} > {data?.val} </td>
                            ))} 
                            </tr>
                        </tbody>                        
                        </table> 
                    </div>
                    : <CircularProgress disableShrink />
                }
                
            </div>
        </React.Fragment>
    );
};

export default Dashboard02;