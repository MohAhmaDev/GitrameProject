import React, { useEffect, useState } from 'react'
import axiosClient from '../../axios-client';
import { Box,colors, Typography, IconButton, Button, CircularProgress } from '@mui/material';

import NivoChar from '../MUI/NivoChar';
import NivoBar from '../MUI/NivoBar';
import NivoLine from '../MUI/NivoLine';
import NivoRadar from '../MUI/NivoRadar';

import DownloadOutlinedIcon from "@mui/icons-material/DownloadOutlined";
import Woman2Icon from '@mui/icons-material/Woman2';
import ManIcon from '@mui/icons-material/Man';
import PercentIcon from '@mui/icons-material/Percent';
import { useStateContext } from '../../contexts/ContextProvider';
import { Link } from 'react-router-dom';


export default function DashboardGlobal() {

  const [key, setKey] = useState([]);
  const [post, setPost] = useState({});
  const [dataCa, setDataCa] = useState();
  const [myData, setMyData] = useState({});
  const [mykey, setMykey] = useState();
  const [stats, setStats] = useState();
  const [radarData, setRadarData] = useState({});
  const [dashFormation, setDashFormation] = useState({});


  const [makeLine, setMakeLine] = useState([]);
  const [dash01, setDash01] = useState({});
  const [dash03, setDash03] = useState({});
  const [dash02, setDash02] = useState({});
  const [dash04, setDash04] = useState({});

  

  


  const getData = () => {
    axiosClient.post('/dash').then(({data}) => {
      setDash02(data.dash02)
      setDash03(data.dash03)
      setDash01(data.dash01)
      setDash04(data.dash04)
    }).catch((error) => {
      console.log(error)
    })
  }

  const getLineData = () => {
    axiosClient.get('/dash-line').then(({data}) => {
        setMakeLine(data);
    }).catch((error) => {
        console.log(error)
    })
  }

  const getCa = () => {
    axiosClient.get('/dash-ca').then((data) => {
      setDataCa(data.data)
    }).catch((err) => console.log(err))
  };

  const getFinanceDashData = () => {
    axiosClient.get('/dash_finance_bar').then(({data}) => {
        setMyData(data.data);
        setMykey(data.column);
        setStats(data.stats)
    }).catch((error) => {
        console.log(error)
    })
  }

  const getRadarData = () => {
    axiosClient.get('/dash_finance_radar').then(({data}) => {
      setRadarData(data)
    }).catch((error) => {
      console.log(error)
    })
  }

  const getDashFormation = () => {
    axiosClient.get('/dash_fformation_pie').then(({data}) => {
      setDashFormation(data)
    }).catch((error) => {
      console.log(error)
    })
  }

  useEffect(() => {
    getData();
    getLineData();
    getCa();
    getFinanceDashData();
    getRadarData();
    getDashFormation();
  }, [])

  useEffect(() => {
    if (Object.keys(dash02).length !== 0) {
      let cle = [];
      let formattedData = {};

      dash02.forEach(function(objet) {
        cle.push(Object.keys(objet)[0]);
        const valeur = objet[cle];
      });
      setKey(cle)

      for (let i = 0; i < dash02.length; i++) {
        let key = Object.keys(dash02[i])[0];
        let value = parseInt(dash02[i][key]);
        formattedData[key.toString()] = value;
      }
      setPost(formattedData)
    }
    // key optional
    (key.length !== 0) && console.log(key);
    // tranche-age / effectifs
    (Object.keys(post).length !== 0) && console.log("age-emp", post);
    // CDI - CDD / NBemployes
    (Object.keys(dash03).length !== 0) && console.log("cdi-cdd", dash03);
    // Line Dash
    (Object.keys(dashFormation).length !== 0) && console.log("fformation-nbr", dashFormation);
    // Line Dash 02
    (makeLine.length !== 0) && console.log(("line --", makeLine));
    // Sexe / Effectifs
    (Object.keys(dash01).length !== 0) && console.log("sexe: ", dash01);
    // Position / NBemployes
    (Object.keys(dash04).length !== 0) && console.log("position: ", dash04);


  }, [dash02])


  return (
    <>
        {((key.length !== 0) && (Object.keys(post).length !== 0) && (Object.keys(dash03).length !== 0)
        && (makeLine.length !== 0) && (Object.keys(myData).length !== 0) 
        && (Object.keys(radarData).length !== 0) && (Object.keys(dashFormation).length !== 0)) ?
        <div>
          <h1> Dashboard </h1>
          <div className="card animated fadeInDown" style={{ marginTop: "25px" }}>
            <h2 style={{
                marginBottom: "20px"
            }}> Graphes et Statistique sur les différents module du Groupe </h2>
            <Box display="grid" gridTemplateColumns="repeat(12, 1fr)" gridAutoRows="170px" gap="20px"> 
              <Box  
                gridColumn="span 4"
                gridRow="span 2"
                // backgroundColor={colors.grey['100']}
                border={"1px solid #2196f3"}
                borderRadius="5px"
                display="flex" 
                justifyContent="space-between"
                paddingTop="25px"
                paddingBottom="50px" 
                alignItems="center" 
                flexDirection="column"
              >
                <h2> Montants Dettes/Creances </h2>
                <Box sx={{ height: "60px", width: "300px", border:"1px solid #2196f3", borderRadius: "5px" ,
                  textAlign: "center", padding: "15px 0 15px 0", color: colors.blue[700]
                }}>
                  <h3> Montant Total Dettes : {stats[0]} </h3>
                </Box>
                <Box sx={{ height: "60px", width: "300px", border:"1px solid #2196f3", borderRadius: "5px" ,
                  textAlign: "center", padding: "15px 0 15px 0", color: colors.blue[700]
                }}>
                  <h3> Montant Total Creances : {stats[1]} </h3>
                </Box>                
                <Box sx={{ height: "60px", width: "300px", border:"1px solid #2196f3", borderRadius: "5px" ,
                  textAlign: "center", padding: "15px 0 15px 0", color: colors.blue[700]
                }}>
                  <h3> Montant Total Factures : {stats[2]} </h3>
                </Box>                
              </Box>
              {/* BOX SP 2.0 */}
              <Box  
                gridColumn="span 4"
                gridRow="span 2"
                // backgroundColor={colors.grey['100']}
                border={"1px solid #2196f3"}
                borderRadius="5px">
                  <Typography
                      variant="h5"
                      fontWeight="600"
                      sx={{ padding: "10px 10px 0 30px" }}
                  >
                    Stats Fianance
                  </Typography>
                  <Box height="350px" mt="-10px" marginRight={"20px"}>
                      <NivoRadar data={radarData}/>
                  </Box>
              </Box>
              {/* BOX SP 3.0 */}
              <Box
                gridColumn="span 4"
                gridRow="span 2"
                border={"1px solid #2196f3"}
                borderRadius="5px"
                  >
                      <Typography
                          variant="h5"
                          fontWeight="600"
                          sx={{ padding: "10px 10px 0 30px" }}
                      >
                        Formation Répartition
                      </Typography>
                      <Box height="300px" mt="0px">
                          <NivoChar data={dashFormation} dash02={true}/>
                      </Box>
              </Box> 
              <Box gridColumn="span 4" gridRow="span 2" backgroundColor={"#007bff"} overflow="auto" borderRadius={`5px`}>
                  <Box 
                  display="flex" 
                  justifyContent="space-between" 
                  alignItems="center" 
                  borderBottom={`2px solid ${colors.grey['A100']}`}
                  colors={colors.grey[100]}
                  p="15px"
                  > 
                      <Typography 
                      color={colors.grey['200']}
                      variant="h5"
                      fontWeight="600">
                          Nombre d'employes par sex
                      </Typography>
                  </Box>
                  <Box p="20px" >
                      <Box p="15px" display="flex" justifyContent="space-between" alignItems="center"
                          backgroundColor={colors.grey['200']} borderRadius="4px">
                          {dash01.map(dash => (
                            <Box fontWeight="500" variant="h3" key={dash.nb_employes}>
                              {dash.Sexe} {dash.Sexe === "Homme" ? <ManIcon/> : <Woman2Icon/>}
                            </Box>
                          ))}
                      </Box>
                      <Box p="15px" display="flex" justifyContent="space-between" alignItems="center">
                          {dash01.map(dash => (
                            <Typography 
                                key={dash.nb_employes}
                                color={colors.grey[200]}
                                variant="h4"
                                fontWeight="600">
                                    {dash.nb_employes}
                            </Typography>                          
                          ))}

                      </Box>
                      <Box p="15px" display="flex" justifyContent="center" alignItems="center"
                          backgroundColor={colors.grey['200']} borderRadius="4px">
                          <Box fontWeight="500" variant="h2">
                              Taux <PercentIcon />
                          </Box>
                      </Box>
                      <Box p="15px" display="flex" justifyContent="space-between" alignItems="center">

                        {dash01.map(dash => (
                          <Typography 
                              key={dash.nb_employes}
                              color={colors.grey[200]}
                              variant="h4"
                              fontWeight="600">
                                  { 
                                    (dash.nb_employes*100 / (parseInt(dash01[0].nb_employes) + parseInt(dash01[1].nb_employes))).toFixed()
                                  }
                          </Typography>                        
                        ))}

                      </Box>
                  </Box>
                  <Box 
                  display="flex" 
                  justifyContent="space-between" 
                  alignItems="center" 
                  borderBottom={`2px solid ${colors.grey['A100']}`}
                  colors={colors.grey[100]}
                  p="15px"
                  > 
                      <Typography 
                      color={colors.grey['200']}
                      variant="h5"
                      fontWeight="600">
                          Employée 2023
                      </Typography>
                  </Box>
                  <Box p="20px" borderBottom={`2px solid ${colors.grey['A100']}`}>
                      <Box p="15px" display="flex" justifyContent="space-between" alignItems="center"
                          backgroundColor={colors.grey['200']} borderRadius="4px">
                          <Box fontWeight="500" variant="h3">
                              Retraité
                          </Box>
                          <Box fontWeight="500" variant="h3">
                              Recruté
                          </Box>
                      </Box>
                      <Box p="15px" display="flex" justifyContent="space-between" alignItems="center">
                          <Typography 
                              color={colors.grey[200]}
                              variant="h4"
                              fontWeight="600">
                                  15
                          </Typography>
                          <Typography 
                              color={colors.grey[200]}
                              variant="h4"
                              fontWeight="600">
                                  26
                          </Typography>
                      </Box>
                      <Box p="15px" display="flex" justifyContent="center" alignItems="center"
                          backgroundColor={colors.grey['200']} borderRadius="4px">
                          <Box fontWeight="500" variant="h2">
                              Taux de Recrutement (<PercentIcon />)
                          </Box>
                      </Box>
                      <Box p="15px" display="flex" justifyContent="center" alignItems="center">
                          <Typography 
                              color={colors.grey[200]}
                              variant="h4"
                              fontWeight="600">
                                  175%
                          </Typography>
                      </Box>
                  </Box>
              </Box>    
              <Box 
                gridColumn="span 8"
                gridRow="span 2"
                backgroundColor={colors.grey[200]}
                  >
                      <Box
                          mt="25px"
                          p="0 30px"
                          display="flex"
                          justifyContent="space-between"
                          alignItems="center"
                      >
                          <Box>
                              <Typography 
                              variant="h5" 
                              fontWeight="600" 
                              color={colors.grey[900]}>
                                  Revenue Generer
                              </Typography>
                              <Typography 
                              variant="h3" 
                              fontWeight="bold" 
                              color={"#007bff"}>
                                  { dataCa && dataCa } DA
                              </Typography>
                          </Box>
                          <Box>
                              <IconButton>
                                  <DownloadOutlinedIcon
                                  sx={{ fontSize: "26px", color: "#007bff" }}
                                  />
                              </IconButton>
                          </Box>
                      </Box>
                      <Box height="250px" m="-20px 0 0 0">
                          <NivoLine data={makeLine}/>
                      </Box>
              </Box>
              <Box  
                gridColumn="span 12"
                gridRow="span 2"
                border={"1px solid #2196f3"}
                borderRadius="5px">
                  {((mykey && mykey.length !== 0) && (Object.keys(myData).length !== 0)) && <>
                    <Typography
                        variant="h5"
                        fontWeight="600"
                        sx={{ padding: "10px 10px 0 30px" }}
                    >
                        Realisation/Privision Finance
                    </Typography>
                    <Box height="300px" mt="-10px" padding={"0 25px 0 25px"}>
                      <NivoBar data={myData} columns={mykey} index={"Mois"} 
                      legend={"Chiffre d'affaire"} enableLabel={false} innerPadding={0} padding={0.3}/>
                    </Box>
                </>}
              </Box>
              <Box
                gridColumn="span 8"
                gridRow="span 2"
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
                    <NivoBar data={[post]} columns={key} index={"tranche d'age"} legend={'Nombre effectifs'}
                    innerPadding={10} padding={0.01}/>
                </Box>
              </Box> 
              <Box
                gridColumn="span 4"
                gridRow="span 2"
                backgroundColor={colors.grey['100']}
                borderRadius="5px"
                  >
                      <Typography
                          variant="h5"
                          fontWeight="600"
                          sx={{ padding: "10px 10px 0 30px" }}
                      >
                        Nombre CDI-CDD
                      </Typography>
                      <Box height="300px" mt="-10px">
                          <NivoChar data={dash03}/>
                      </Box>
              </Box>         
            </Box>     
          </div>
        </div>
        : <div className="card animated fadeInDown"
            style={{ display: "flex", flexWrap: "wrap", justifyContent: "space-between"}}
          >

              <div class="card-box-1">
                <p class="skeleton-text-1 skeleton-anim"></p>
                <p class="skeleton-block skeleton-anim"></p>
              </div>
              <div class="card-box-0">
                <p class="skeleton-text-1 skeleton-anim"></p>
                <p class="skeleton-block skeleton-anim"></p>
              </div>   

              <div class="card-box-0">
                <p class="skeleton-text-1 skeleton-anim"></p>
                <p class="skeleton-block skeleton-anim"></p>
              </div>  
              <div class="card-box-1">
                <p class="skeleton-pie skeleton-anim"></p>
              </div>       
          </div>
        }
    </> 
  )
}
