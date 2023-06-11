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
import React, { useEffect, useState } from 'react';
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
import { pink } from '@mui/material/colors';
import NivoBar from '../MUI/NivoBar';
import NivoChar from '../MUI/NivoChar';
import { useDisplayContext } from '../../contexts/DisplayContext';


const Dashboard01 = () => {

    const [effectifs, setEffectifs] = useState({});
    const [sociopro, setSociopro] = useState({});
    const [trancheAge, setTrancheAge] = useState({});
    const [contrat_Dash, setCotrat_Dash] = useState({});
    const [check1, setCheck1] = useState(true);
    const [check2, setCheck2] = useState(true);

    const [key, setKey] = useState([]);
    const [post, setPost] = useState({});


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
        getData(data);
        getDash(data);
    }, [data])

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
            filiale: "",
            date: ""
        })
    }


    return (
        <>
            <h1> Dashboard RHS </h1>

            <div className="card animated fadeInDown" style={{ marginTop: "20px" }}>
            {(
            (Object.keys(post).length !== 0) && (Object.keys(contrat_Dash).length !== 0)
            && (Object.keys(effectifs).length !== 0) && (Object.keys(sociopro).length !== 0)
            ) ? 
            <Box 
                display="grid"
                gridTemplateColumns="repeat(12, 1fr)"
                // gridRow={"175px 175px"}
                // gridTemplateRows="100px 100px 100px 175px"
                gap="20px"
            >
                <Box
                    padding={"10px"}
                    gridColumn="span 12" 
                >
                    <form>
                        <Box m="25px" display="grid" gridTemplateColumns="repeat(6, minmax(0, 1fr))" gap="30px" > 
                            <label style={{ gridColumn: "span 1" }}> RHS : (filitre filiale) </label>
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
                        <Box m="25px" display="grid" gridTemplateColumns="repeat(6, minmax(0, 1fr))" gap="30px" > 
                        <label style={{ gridColumn: "span 1" }}> RHS : (filitre Annee) </label>
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
                    </form>
                </Box>

                {/* ROW 1 */}

                {check2 && effectifs.map(dash => (
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
                    title="28"
                    subtitle="Total"
                    icon={
                        <WorkIcon sx={{ color: "#FFFFFF", fontSize: "30px"}}/>
                    }
                    />
                </Box>}
                {check1 && sociopro.map(dash => (
                    <Box 
                    padding={"10px"}                    
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
                    </FormGroup>
                </Box>


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
                    padding={"10px"}
                    gridColumn="span 12"
                >
                <Box display="flex" justifyContent="end" mt="20px" m="10px">
                    <Button type="submit" color="primary" variant="contained" 
                    onClick={handleSubmit(onSubmit)}>
                        Renaitialiser
                    </Button>
                </Box>
                </Box>
            </Box> :<CircularProgress disableShrink />}

            </div>
        </>
    );
};

export default Dashboard01;