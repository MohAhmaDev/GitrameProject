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
import React, { useEffect, useRef, useState } from 'react';
import { useForm, Controller } from "react-hook-form";
import StatBox from '../MUI/StatBox';
import DownloadOutlinedIcon from "@mui/icons-material/DownloadOutlined";
import MaleIcon from '@mui/icons-material/Male';
import FemaleIcon from '@mui/icons-material/Female';
import BusinessCenterIcon from '@mui/icons-material/BusinessCenter';
import WorkIcon from '@mui/icons-material/Work';
import GroupIcon from '@mui/icons-material/Group';
import EngineeringIcon from '@mui/icons-material/Engineering';
import axiosClient from '../../axios-client';
import { blue, orange, pink } from '@mui/material/colors';
import NivoBar from '../MUI/NivoBar';
import NivoChar from '../MUI/NivoChar';
import { useDisplayContext } from '../../contexts/DisplayContext';
import { useReactToPrint } from "react-to-print";
import SailingIcon from '@mui/icons-material/Sailing';
import TemplateTest from '../TableRHS';
import { useStateContext } from '../../contexts/ContextProvider';
import GitramReports from '../MUI/GitramReports';
import { useNavigate } from 'react-router-dom';

const Dashboard01 = () => {

    const navigate = useNavigate();
    const conponentPDF = useRef();
    const { fetchUser, filiale, role } = useStateContext()

    const [fformation, setFformation] = useState({
        type_formation: {},
        Montant: null,
        NB_personne: null,
    });
    const [effectifs, setEffectifs] = useState({});
    const [sociopro, setSociopro] = useState({});
    const [trancheAge, setTrancheAge] = useState({});
    const [contrat_Dash, setCotrat_Dash] = useState({});
    const [check1, setCheck1] = useState(true);
    const [check2, setCheck2] = useState(true);
    const [check3, setCheck3] = useState(true);


    const [key, setKey] = useState([]);
    const [post, setPost] = useState({});
    const [total, setTotal] = useState({});


    const [dates, setDates] = useState({});
    const {filiales, getFiliales, setFiliales} = useDisplayContext()
    const [data, setData] = useState({
        filiale: "",
        date: ""
    });

    const getDash = (req) => {
        axiosClient.post('/dash', req).then(({data}) => {
            setCotrat_Dash(data.dash03)
            setTrancheAge(data.dash02)
          }).catch((error) => {
            console.log(error)
          })
    }

    const getData = (req) => {
        axiosClient.post('rhs_dashboard', req).then(({data}) => {
            setSociopro(data.ebe2);
            setEffectifs(data.ebe1);
            setTotal(data.total)
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

    const getFformation = (req) => {
        axiosClient.post('/dash_formation', req).then(({data}) => {
            setFformation({
                type_formation: data.type_formation,
                Montant: data.Montant,
                NB_personne: data.NB_personne
            })
        }).catch((err) => {
            console.log(err)
        })        
    }


    useEffect(() => {
        getFformation(data);
        getDates()
        getFiliales();
        getData(data);
        getDash(data);
    }, [data])

    useEffect(() => {
        setData({...data, filiale: filiale?.id})
    }, [filiale])

    useEffect(() => {
        if (Object.keys(trancheAge).length !== 0) {
            let cle = [];
            let formattedData = {};
        
            trancheAge .forEach(function(objet) {
                cle.push(Object.keys(objet)[0]);
                const valeur = objet[cle];
            });
            setKey(cle)
        
            for (let i = 0; i < trancheAge .length; i++) {
                let key = Object.keys(trancheAge [i])[0];
                let value = parseInt(trancheAge [i][key]);
                formattedData[key.toString()] = value;
            }
            setPost(formattedData)
        }
    }, [trancheAge])

    const { handleSubmit, control, 
        register, getValues, watch , setValue, reset, formState, setError, formState: { errors } } = useForm({
        mode: "onChange"
    });

    const onSubmit = () => {
        setData({
            filiale: filiale?.id,
            date: ""
        })
    }

    useEffect(() => {
        fetchUser()
    }, [])

    console.log(fformation)

    const generatePDF= useReactToPrint({
        content: ()=>conponentPDF.current,
        documentTitle:"Userdata",
        onAfterPrint:()=>alert("Data saved in PDF")
    });

    return (
        <>
        <h1> Dashboard RHS </h1>

        {((Object.keys(post).length !== 0) && (Object.keys(contrat_Dash).length !== 0) && filiale) 
        ? <div className="card animated fadeInDown" style={{ marginTop: "20px" }}>
        <h1 style={{ marginLeft: "5px" }}> Indicateur de Performances Resource Humaine </h1>
        <Box 
            display="grid"
            gridTemplateColumns="repeat(12, 1fr)"

            gap="20px"
            
        >
            <Box
                padding={"10px"}
                gridColumn="span 12" 
                justifyContent={"space between"}
            >
                <form>
                        <label style={{ gridColumn: "span 1" }}> </label>
                        {(!filiale?.id) && <Controller
                            control={control}
                            name="filiale"
                            render={({ field: { onChange } }) => (
                                <FormControl variant="outlined" sx={{ width: "300px" }}>
                                <InputLabel id="demo-simple-select-label"> Filtre Filiale  </InputLabel>
                                <Select
                                    sx={{ gridColumn: "span 2" }}
                                    label="Filtre Filiale "
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
                        /> }
                   <label style={{ gridColumn: "span 1", marginLeft: "20px" }}>  </label>
                    <Controller
                        control={control}
                        name="date"
                        render={({ field: { onChange } }) => (
                            <FormControl variant="outlined" sx={{ width: "300px" }}>
                            <InputLabel> Filtre Annee  </InputLabel>
                            <Select
                                sx={{ gridColumn: "span 2" }}
                                label="Filtre annee"
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
                    {/* </Box>  */}
                </form>
            </Box>

            {/* ROW 1 */}

            {(check2 && Object.keys(effectifs).length !== 0) && effectifs.map(dash => (
            <Box 
            padding={"10px"}
            gridColumn="span 2" 
            backgroundColor={"#9932CC"} 
            display="flex" 
            alignItems="center"
            justifyContent="center"> 
                <StatBox 
                title={dash.val}
                subtitle={dash.key}
                icon={
                    dash.key === "homme" ? <MaleIcon sx={{ color: "#FFFFFF", fontSize: "30px" }} /> :
                    dash.key === "femme" ? <FemaleIcon sx={{ color: "#FFFFFF", fontSize: "30px" }} /> :
                    dash.key === "personnelle administratifs" ? <BusinessCenterIcon sx={{ color: "#FFFFFF", fontSize: "30px" }} /> :
                    dash.key === "personnelle technique" ? <EngineeringIcon sx={{ color: "#FFFFFF", fontSize: "30px" }} /> :
                    null
                }
                />
            </Box>))}

            {check2 && <Box 
                gridColumn="span 2" 
                padding={"10px"}                    
                backgroundColor={"#9932CC"} 
                display="flex" 
                alignItems="center"
                justifyContent="center"> 
                <StatBox 
                title={total[0]?.nb_employes ? total[0]?.nb_employes : 0}
                subtitle="Total"
                icon={
                    <WorkIcon sx={{ color: "#FFFFFF", fontSize: "30px"}}/>
                }
                />
            </Box>}

            {check3 && (Object.keys(fformation.type_formation).length !== 0) && 
                fformation.type_formation.map(dash => (
            <Box 
            minHeight={"100px"}
            padding={"10px"}
            gridColumn="span 2" 
            backgroundColor={"#1e88e5"} 
            display="flex" 
            alignItems="center"
            justifyContent="center"> 
                <StatBox 
                title={dash.nb_effectif}
                subtitle={dash.Domaine}
                icon={
                    <SailingIcon sx={{ color: "#FFFFFF", fontSize: "30px" }} />
                }
                />
            </Box>))}

            {(check1 && Object.keys(sociopro).length !== 0) && sociopro.map(dash => (
                <Box 
                padding={"10px"}   
                minHeight={"100px"}
                gridColumn="span 2" 
                gridRow="span 1"
                backgroundColor={"#FA8072"} 
                display="flex" 
                alignItems="center"
                justifyContent="center"> 
                <StatBox 
                title={dash?.val}
                subtitle={dash?.key}
                icon={
                    <GroupIcon sx={{ color: "#FFFFFF", fontSize: "30px"}}/>
                }
                />
            </Box>
            ))}


            <Box 
            gridColumn="span 12" 
            >
                <FormGroup>
                    <FormControlLabel control={<Checkbox checked={check2} onClick={ev => { setCheck2(c => !c) }}
                    defaultChecked color='secondary' />} label="effectifs" />
                    <FormControlLabel control={<Checkbox checked={check1} onClick={ev => { setCheck1(c => !c) }}
                    sx={{
                        color: pink[800],
                        '&.Mui-checked': {
                            color: pink[600],
                        },
                    }}
                    />} label="socioprofessionnelle" />
                    <FormControlLabel control={<Checkbox checked={check3} onClick={ev => { setCheck3(c => !c) }}
                    sx={{
                        color: blue[800],
                        '&.Mui-checked': {
                            color: blue[600],
                        },
                    }}
                    />} label="Formation " />
                </FormGroup>
            </Box>

            {(role && role === "global") && <>
            <Box
                minHeight={"175px"}
                gridColumn="span 8"
                backgroundColor={colors.grey['100']}
                borderRadius="5px"
                >
                <Typography
                    variant="h5"
                    fontWeight="600"
                    sx={{ padding: "10px 10px 0 30px" }}
                >
                    Tranches d'age des employes
                </Typography>
                <Box height="300px" mt="-10px">
                    <NivoBar data={post} columns={key}/>
                </Box>
            </Box> 
          
            <Box
                minHeight={"175px"}
                gridColumn="span 4"
                backgroundColor={colors.grey['100']}
                borderRadius="5px"
                >
                    <Typography
                        variant="h5"
                        fontWeight="600"
                        sx={{ padding: "10px 10px 0 30px" }}
                    >
                        CDI-CDD
                    </Typography>
                    <Box height="300px" mt="-10px">
                        <NivoChar data={contrat_Dash}/>
                    </Box>
            </Box>  
            <Box
                minHeight={"175px"}
                gridColumn="span 12"
                borderRadius="5px"
                >
                    <TemplateTest />
            </Box>  
            <Box
                padding={"10px"}
                gridColumn="span 12"
            >
            <Box display="flex" justifyContent="end" mt="20px" m="10px">
                <Button type="submit" color="primary" variant="contained" 
                onClick={handleSubmit(onSubmit)}>
                    Renaitialiser
                </Button>
                <Button variant='contained' style={{ marginLeft: "10px" }} color="error"
                onClick={() => navigate('/report')}> PDF REPORT </Button> 

            </Box>
            </Box>
            </>}
            
        </Box> 
        </div> :<CircularProgress disableShrink sx={{ marginTop: "50px" }}/>}
    </>
    );
};

export default Dashboard01;

// #ef6c00                            
