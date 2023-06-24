import React, { useEffect, useMemo, useRef, useState } from 'react';
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
import { useStateContext } from '../../contexts/ContextProvider';



const Dashboard03 = () => {

    const conponentPDF = useRef();
    const { fetchUser, filiale } = useStateContext();

    const [isLoading, setIsLoading] = useState(false);

    const [dates, setDates] = useState({});
    const [Form, setForm] = useState({
        filiale: null,
        date: ""
    });
    const [checked, setChecked] = useState({
        creances: true,
        dettes: false,
        creances_vs_dettes: false
    });
    const [data1, setData1] = useState({});
    const [data2, setData2] = useState({});
    const [data3, setData3] = useState({});
    const [secteur, setSecteur] = useState({});
    const [groupe, setGroupe] = useState({});
    const { getFiliales, setFiliales, filiales } = useDisplayContext()


    const { handleSubmit, control,
        register, getValues, watch , setValue, reset, formState, setError, formState: { errors } } = useForm({
        mode: "onChange"
    });


    const getData = (req) => {
        axiosClient.post('/dash_creance_dettes', req).then(({data}) => {
            setData1(data.data1);
            setData2(data.data2);
            setData3(data.data3);
            setGroupe(data.groupe);
            setSecteur(data.secteur);
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
                const field = data1.find(item => item?.ID_Ent_A === index && item?.ID_Ent_B === i);
                const montantCreances = field ? field.Montant_Creances : "-";
                const montantDettes = field ? field.Montant_Dettes : "-";
                const CreancesVSDettes = field ? field.Creances_vs_Dettes : "-";

                field && cells.push(
                <td key={`cell-${index}-${i}`} className="F_">
                    <ul style={{ listStyle: "none", margin: "0px", padding: "0px" }}>
                        {checked.creances && <li> {montantCreances} </li>}
                        {checked.dettes && <li> {montantDettes} </li>}
                        {checked.creances_vs_dettes && <li> {CreancesVSDettes} </li>}
                    </ul>
                </td>);
            }
            rows.push(<tr key={`row-${index}`}>{cells}</tr>);
        }
        return rows;
    };

    console.log(data1)


    const getTable2 = () => {
        const rows = [];
      
        for (let index = 1; index < 19; index++) {
          const cells = [];
      
          if (Form.filiale) {
            if (Form.filiale === index) {
              cells.push(<th key={`header-${index}`} className="F_">F-{index}</th>);
            }
          } else {
            cells.push(<th key={`header-${index}`} className="F_">F-{index}</th>);
          }
      
          groupe.forEach(element => {
            const field = data2.find(item => item?.ID_Ent_A === index && item?.Grp_Ent === element.Grp_Ent);
            const montantCreances = field ? field.Montant_Creances : "-";
            const montantDettes = field ? field.Montant_Dettes : "-";
            const CreancesVSDettes = field ? field.Creances_vs_Dettes : "-";
      
            if (field) {
              cells.push(
                <td key={`cell-${index}-${element.Grp_Ent}`} className="F_">
                  <ul style={{ listStyle: "none", margin: "0px", padding: "0px" }}>
                    {checked.creances && <li>{montantCreances}</li>}
                    {checked.dettes && <li>{montantDettes}</li>}
                    {checked.creances_vs_dettes && <li>{CreancesVSDettes}</li>}
                  </ul>
                </td>
              );
            }
          });
      
          rows.push(<tr key={`row-${index}`}>{cells}</tr>);
        }
      
        return rows;
    };
      
    const getTable3 = () => {
        const rows = [];
      
        for (let index = 1; index < 19; index++) {
          const cells = [];
      
          if (Form.filiale) {
            if (Form.filiale === index) {
              cells.push(<th key={`header-${index}`} className="F_">F-{index}</th>);
            }
          } else {
            cells.push(<th key={`header-${index}`} className="F_">F-{index}</th>);
          }
      
          secteur.forEach(element => {
            const field = data3.find(item => item?.ID_Ent_A === index && item?.Sect_Ent === element.Sect_Ent);
            const montantCreances = field ? field.Montant_Creances : "-";
            const montantDettes = field ? field.Montant_Dettes : "-";
            const CreancesVSDettes = field ? field.Creances_vs_Dettes : "-";
      
            if (field) {
              cells.push(
                <td key={`cell-${index}-${element.Sect_Ent}`} className="F_">
                  <ul style={{ listStyle: "none", margin: "0px", padding: "0px" }}>
                    {checked.creances && <li>{montantCreances}</li>}
                    {checked.dettes && <li>{montantDettes}</li>}
                    {checked.creances_vs_dettes && <li>{CreancesVSDettes}</li>}
                  </ul>
                </td>
              );
            }
          });
      
          rows.push(<tr key={`row-${index}`}>{cells}</tr>);
        }
      
        return rows;
    };

    useMemo(() => {
        fetchUser()
    }, [])

    useEffect(() => {
        if (Form.date === "" || Form.filiale === null) {
            setData1({})
            setData2({})
            setData3({})            
        }
        setIsLoading(true)
        getFiliales();
        getDates();
        getData(Form)
    }, [Form]);

    useEffect(() => {
        setForm({...Form, filiale: filiale?.id})
    }, [filiale])


    const onSubmit = () => {
        setForm({
            filiale: filiale?.id,
            date: ""
        })
        getData();
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
    }

    const generatePDF= useReactToPrint({
        content: ()=>conponentPDF.current,
        documentTitle:"Userdata",
        onAfterPrint:()=>console.log("Data saved in PDF")
    });

    // console.log("filiale : ", filiale)
    // console.log("data3 : ",data3);
    // console.log("data2 : ",data2);
    // console.log("data1 : ",data1);
    // console.log('groupe : ', groupe);
    // console.log('secteur : ', secteur);

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
                        <FormControlLabel control={<Checkbox checked={checked.dettes}
                        onClick={ev => { setChecked({...checked, dettes: !checked.dettes}) }}/>} label="dettes" />
                        <FormControlLabel control={<Checkbox checked={checked.creances_vs_dettes}
                        onClick={ev => { setChecked({...checked, creances_vs_dettes: !checked.creances_vs_dettes}) }}/>}
                        label="creances_vs_dettes" />
                    </FormGroup>
                </Box>
                <Box>
                    {(Object.keys(data2).length !== 0 && Object.keys(data1).length !== 0 
                    && Object.keys(data3).length !== 0) && <Box m="25px">
                        {(!filiale?.id) && <Controller
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
                        />}
                    </Box>}
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
                                    value={Form.date}
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
            <div ref={conponentPDF}>
                <h2 style={{ textAlign: "center", marginBottom: "25px" }}> Tableaux Intra Groupe Gitrama  </h2>
                {Object.keys(data1).length !== 0 ?
                <table className="ftable" id="ftable" style={{ marginBottom: "25px" }}>
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
                <h2 style={{ textAlign: "center", marginBottom: "25px" }}>  Tableaux Récapulatif par Groupe </h2>
                {(Object.keys(data2).length !== 0) ?
                <table className="ftable" id="ftable" style={{ marginBottom: "25px" }}>
                    <thead>
                        <tr>
                            <th className="F_"></th>
                            {Object.keys(groupe).length !== 0 && groupe.map((data) => (
                                <th key={data.Grp_Ent} className="F_">
                                    {data.Grp_Ent}
                                </th>
                            ))}
                        </tr>
                    </thead>
                    <tbody>
                        {getTable2()}
                    </tbody>
                </table>: <CircularProgress disableShrink />}
                <h2 style={{ textAlign: "center", marginBottom: "25px" }}> Tableaux Récapulatif par Secteur </h2>
                {(Object.keys(data3).length !== 0) ?
                <table className="ftable" id="ftable">
                    <thead>
                        <tr>
                            <th className="F_"></th>
                            {Object.keys(secteur).length !== 0 && secteur.map((data) => (
                                <th key={data.Sect_Ent} className="F_">
                                    {data.Sect_Ent}
                                </th>
                            ))}
                        </tr>
                    </thead>
                    <tbody>
                        {getTable3()}
                    </tbody>
                </table>: <CircularProgress disableShrink />}
            </div>
            <Box display="flex" justifyContent="end" mt="20px" m="25px">
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
