import React, { useEffect, useRef, useState } from 'react';
import axiosClient from '../../axios-client';
import { useDisplayContext } from '../../contexts/DisplayContext';
import { Box,
    Button,
    IconButton,
    Typography,
    CircularProgress,
    colors,
    FormControlLabel,
    FormGroup,
    Checkbox,
    FormControl,
    InputLabel,
    Select,
    MenuItem
} from '@mui/material';
import { useForm, Controller } from "react-hook-form";
import 'table2excel';
import { useReactToPrint } from "react-to-print";



const Dashboard03 = () => {

    const conponentPDF = useRef();
    const [isLoading, setIsLoading] = useState(true);

    const [dates, setDates] = useState({});
    const [Form, setForm] = useState({
        filiale: "",
        date: ""
    });
    const [checked, setChecked] = useState({
        creances: true,
        dettess: false,
        creances_vs_dettes: false
    });
    const [data, setData] = useState({});
    const { getFiliales, setFiliales, filiales } = useDisplayContext()


    const { handleSubmit, control,
        register, getValues, watch , setValue, reset, formState, setError, formState: { errors } } = useForm({
        mode: "onChange"
    });


    const getData = (req) => {
        axiosClient.post('/dash_creance_dettes', req).then(({data}) => {
            setData(data);
            }).catch((error) => {
            console.log(error)
        })
    }

    const getDates = () => {
        axiosClient.get('/date_fcreance_dettes').then(({data}) => {
            setDates(data)
        }).catch((err) => {
            console.log(err)
        })
    }

    const getTable = () => {
        const rows = [];
        for (let index = 1; index < 19; index++) {
            const cells = [];
            if (Form.filiale) {
                (Form.filiale === index) && cells.push(<th key={`header-${index}`} className="F_">F-{index}</th>);
            } else {
                cells.push(<th key={`header-${index}`} className="F_">F-{index}</th>);
            }
            
            for (let i = 1; i < 19; i++) {
                const field = data.find(item => item?.ID_Ent_A === index && item?.ID_Ent_B === i);
                const montantCreances = field ? field.Montant_Creances : "-";
                const montantDettes = field ? field.Montant_Dettes : "-";
                const CreancesVSDettes = field ? field.Creances_vs_Dettes : "-";

                field && cells.push(
                <td key={`cell-${index}-${i}`} className="F_">
                    <ul style={{ listStyle: "none", margin: "0px", padding: "0px" }}>
                        {checked.creances && <li> {montantCreances} </li>}
                        {checked.dettess && <li> {montantDettes} </li>}
                        {checked.creances_vs_dettes && <li> {CreancesVSDettes} </li>}
                    </ul>
                </td>);
            }
            rows.push(<tr key={`row-${index}`}>{cells}</tr>);
        }
        return rows;
    };

    useEffect(() => {
        setIsLoading(true)
        setData({});
        getFiliales();
        getDates();
        getData(Form)
    }, [Form]);

    const onSubmit = () => {
        setForm({
            filiale: null,
            date: ""
        })
        getData();
    }


    const downloadReport = () => {
        const invoice = document.getElementById("ftable");
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

    return (
        <div style={{ background: "#fff", padding: "20px", borderRadius: "5px" }}>
            <Box padding={"20px"}
                display="grid"
                gridTemplateColumns="repeat(12, 1fr)"
                gap="20px"
            >
                <Box
                    padding={"10px"}
                    gridColumn="span 6"
                >
                    <h2> Filtrer les resultats </h2>
                    <FormGroup>
                        <FormControlLabel control={<Checkbox checked={checked.creances}
                        onClick={ev => { setChecked({...checked, creances: !checked.creances}) }}/>} label="creances" />
                        <FormControlLabel control={<Checkbox checked={checked.dettess}
                        onClick={ev => { setChecked({...checked, dettess: !checked.dettess}) }}/>} label="dettes" />
                        <FormControlLabel control={<Checkbox checked={checked.creances_vs_dettes}
                        onClick={ev => { setChecked({...checked, creances_vs_dettes: !checked.creances_vs_dettes}) }}/>}
                        label="creances_vs_dettes" />
                    </FormGroup>
                </Box>
                <Box>
                    <Box m="25px">
                        <Controller
                            control={control}
                            name="filiale"
                            render={({ field: { onChange } }) => (
                                <FormControl variant="outlined" sx={{ width: "300px" }}>
                                <InputLabel id="demo-simple-select-label"> Filtre filiale  </InputLabel>
                                <Select
                                    sx={{ gridColumn: "span 2" }}
                                    label="Filtre filiale"
                                    {...register("filiale")}
                                    value={Form.filiale}
                                    onChange={(ev) => setForm({...Form, filiale: ev.target.value})}
                                >
                                    {(Object.keys(filiales).length !== 0) && filiales?.map(filiale => (
                                    <MenuItem value={filiale?.id} key={filiale?.id}> {filiale?.name} </MenuItem>
                                    ))}
                                </Select>
                                </FormControl>
                            )}
                        />
                    </Box>
                    <Box m="25px">
                        <Controller
                            control={control}
                            name="date"
                            render={({ field: { onChange } }) => (
                                <FormControl variant="outlined" sx={{ width: "300px" }}>
                                <InputLabel> Filtre Mois  </InputLabel>
                                <Select
                                    sx={{ gridColumn: "span 2" }}
                                    label="Filtre Mois"
                                    {...register("date")}
                                    value={data.date}
                                    onChange={(ev) => setForm({...Form, date: ev.target.value})}
                                >
                                    {(Object.keys(dates).length !== 0) && dates?.map(date => (
                                    <MenuItem value={date?.id} > {date?.date_mois} </MenuItem>
                                    ))}
                                </Select>
                                </FormControl>
                            )}
                        />
                    </Box>
                </Box>

            </Box>
            <div>
            {Object.keys(data).length !== 0 ?
            <table className="ftable" id="ftable" ref={conponentPDF}>
                <thead>
                    <tr>
                        <th className="F_"></th>
                        {[...Array(18)].map((_, i) => (
                            <th key={`header-${i}`} className="F_">F_{i + 1}</th>
                        ))}
                    </tr>
                </thead>
                <tbody>
                    {getTable()}
                </tbody>
            </table>: <CircularProgress disableShrink />}
            </div>
            <Box display="flex" justifyContent="end" mt="20px" m="10px">
                <Button type="submit" color="primary" variant="contained" 
                onClick={handleSubmit(onSubmit)}>
                    Renaitialiser
                </Button>
                <Button style={{ marginLeft: "10px" }} variant="contained"  onClick={ev => {generatePDF()}} 
                color="error"> PDF REPORT </Button> 
                {/*  / onClick={ev => {exportData()}}>*/}
                <Button style={{ marginLeft: "10px" }} variant="contained" 
                onClick={ev => {exportData()}} color='success'>EXEL REPORT </Button>                  
            </Box>
        </div>
    );


};

export default Dashboard03;
