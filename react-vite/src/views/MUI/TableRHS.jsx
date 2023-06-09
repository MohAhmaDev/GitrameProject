import React, { useEffect, useState } from 'react';
import axiosClient from '../../axios-client';
import { Box, TextField, Button, CircularProgress,
  FormControl, Select, InputLabel, MenuItem} from '@mui/material';
import { get } from 'react-hook-form';



const TableRHS = ({hide = false}) => {

    const [fformation, setFformation] = useState({
        type_formation: {},
        Montant: null,
        NB_personne: null,
    });
    const [effectifs, setEffectifs] = useState({});
    const [sociopro, setSociopro] = useState({});
    const [total, setTotal] = useState({});


    const [trancheAge, setTrancheAge] = useState({});
    const [table, setTable] = useState({});
    const [status, setStatus] = useState({});
    const [filtreStatue, setFiltreStatu] = useState({
      statue: "Cadre"
    });


    const getTrancheAge = (req) => {
        axiosClient.post('/dash_femployes', req).then(({data}) => {
            setTrancheAge(data.tranchAge);
            setTable(data.tab);
            setStatus(data.status)
        }).catch((error) => {
            console.log(error);
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
        getTrancheAge(filtreStatue)
        getData()
        getFformation()
    }, [filtreStatue])


    const getColumnAge = () => {
        const rows = [];
        trancheAge.forEach((element) => {
          const cells = [];
          for (let index = 1; index < 9; index++) {
            let field;
            if (Array.isArray(table)) {
              switch (index) {
                case 1:
                  field = table.find(
                    (item) =>
                      item?.age === element?.Tranche_Age &&
                      item?.contact === 'CDI' &&
                      item?.sexe === 'Femme' &&
                      item?.temps === 'Temps plein'
                  );
                  break;
                case 2:
                  field = table.find(
                    (item) =>
                      item?.age === element?.Tranche_Age &&
                      item?.contact === 'CDI' &&
                      item?.sexe === 'Femme' &&
                      item?.temps === 'Temps partiel'
                  );
                  break;
                case 3:
                  field = table.find(
                    (item) =>
                      item?.age === element?.Tranche_Age &&
                      item?.contact === 'CDI' &&
                      item?.sexe === 'Homme' &&
                      item?.temps === 'Temps plein'
                  );
                  break;
                case 4:
                  field = table.find(
                    (item) =>
                      item?.age === element?.Tranche_Age &&
                      item?.contact === 'CDI' &&
                      item?.sexe === 'Homme' &&
                      item?.temps === 'Temps partiel'
                  );
                  break;
                case 5:
                  field = table.find(
                    (item) =>
                      item?.age === element?.Tranche_Age &&
                      item?.contact === 'CDD' &&
                      item?.sexe === 'Femme' &&
                      item?.temps === 'Temps plein'
                  );
                  break;
                case 6:
                  field = table.find(
                    (item) =>
                      item?.age === element?.Tranche_Age &&
                      item?.contact === 'CDD' &&
                      item?.sexe === 'Femme' &&
                      item?.temps === 'Temps partiel'
                  );
                  break;
                case 7:
                  field = table.find(
                    (item) =>
                      item?.age === element?.Tranche_Age &&
                      item?.contact === 'CDD' &&
                      item?.sexe === 'Homme' &&
                      item?.temps === 'Temps plein'
                  );
                  break;
                case 8:
                  field = table.find(
                    (item) =>
                      item?.age === element?.Tranche_Age &&
                      item?.contact === 'CDD' &&
                      item?.sexe === 'Homme' &&
                      item?.temps === 'Temps partiel'
                  );
                  break;
                default:
                  break;
              }
            }
      
            cells.push(
              <td key={`cell-${index}-${element?.Tranche_Age}`} className="F_">
                {field ? field.nb_effectifs : '-'}
              </td>
            );
          }
          rows.push(
          <tr key={`row-${element?.tranchAge}`}>
            <th className="F_" key={`header-${element?.Tranche_Age}`}> {element?.Tranche_Age} </th>
            {cells}
          </tr>); 
        });
        return rows;
    };

    
    console.log(filtreStatue);
    return (
        <>           
            {(trancheAge && Object.keys(table).length !== 0) ? 
            <div>
              <Box m="25px" display="grid" gridTemplateColumns="repeat(6, minmax(0, 1fr))" gap="30px" > 
                {!hide && <>
                  <label style={{ gridColumn: "span 1" }}> Status Socioprofetionlle </label>
                    <FormControl variant="outlined" sx={{ width: "300px" }}>
                    <InputLabel id="demo-simple-select-label"> status </InputLabel>
                    <Select
                        sx={{ gridColumn: "span 2" }}
                        label="status"
                        value={filtreStatue.statue}
                        onChange={(ev) => setFiltreStatu({...filtreStatue, statue: ev.target.value})}
                        >
                        {(Object.keys(status).length !== 0) && status?.map(statu => (
                        <MenuItem value={statu?.Scocipro} key={statu?.Scocipro}> {statu?.Scocipro} </MenuItem>
                        ))}
                    </Select>
                    </FormControl>
                </>}

              </Box>
              <h2 style={{
                fontFamily: "serif",
                margin: "20px"
              }}> Répartition des éffectifs par tranches d'ages et par catégories socioprofessionnelles </h2>
              <table className="ftable" id="ftable">
                      <thead>
                          <tr>
                              <th className="F_"></th>
                              {[...Array(8)].map((_, i) => (
                                  <th key={`contact-${i}`} className="F_"> {i < 4 ? "CDI" : "CDD"} </th>
                              ))}
                          </tr>
                      </thead>
                      <thead>
                          <tr>
                              <th className="F_"></th>
                              {[...Array(8)].map((_, i) => (
                                  <th key={`sexe-${i}`} className="F_"> 
                                      {[0,1,4,5].includes(i) ? "Femme" : "Homme"} 
                                  </th>
                              ))}
                          </tr>
                      </thead>
                      <thead>
                          <tr>
                              <th className="F_"></th>
                              {[...Array(8)].map((_, i) => (
                                  <th key={`temps-${i}`} className="F_">{i % 2 ? "Temps partiel" : 
                                  "Temps Plain"}</th>
                              ))}
                          </tr>
                      </thead>
                      <tbody>
                          {getColumnAge()}  
                      </tbody>
              </table>
            </div>: <CircularProgress disableShrink />}
            {hide && <div>
            {effectifs && Object.keys(effectifs).length !== 0 ? (
            <div style={{ marginTop: '50px'}}>
                <h2 style={{ marginBottom: '10px', textAlign: 'center' }}>
                    Effectifs
                </h2>
                <table>
                <thead>
                    <tr>
                    {Object.keys(effectifs).length !== 0 &&
                        effectifs.map((data) => <th value={data?.key}> {data?.key} </th>)}
                    </tr>
                </thead>
                <tbody>
                    <tr>
                    {Object.keys(effectifs).length !== 0 &&
                        effectifs.map((data) => <td value={data?.val}> {data?.val} </td>)}
                    </tr>
                </tbody>
                </table>
            </div>
            ) : (
            <CircularProgress sx={{ marginTop: '25px' }} disableShrink />
            )}
            {sociopro && Object.keys(sociopro).length !== 0 ? (
                <div style={{ marginTop: '50px'}}>
                    <h2 style={{ marginBottom: '10px', textAlign: 'center' }}>
                        Catégorie Socioprofetionnelles
                    </h2>
                    <table>
                    <thead>
                        <tr>
                        {Object.keys(sociopro).length !== 0 &&
                            sociopro.map((data) => <th value={data?.key}> {data?.key} </th>)}
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        {Object.keys(sociopro).length !== 0 &&
                            sociopro.map((data) => <td value={data?.val}> {data?.val} </td>)}
                        </tr>
                    </tbody>
                    </table>
                </div>
                ) : (
                <CircularProgress sx={{ marginTop: '25px' }} disableShrink />
            )}
            {fformation.type_formation && Object.keys(fformation.type_formation).length !== 0 ? (
                <div style={{ marginTop: '50px'}}>
                    <h2 style={{ marginBottom: '10px', textAlign: 'center' }}>
                        Catégorie Socioprofetionnelles
                    </h2>
                    <table>
                    <thead>
                        <tr>
                        {Object.keys(fformation.type_formation).length !== 0 &&
                            fformation.type_formation.map((data) => <th value={data?.Domaine}> 
                            {data?.Domaine} </th>)}
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        {Object.keys(fformation.type_formation).length !== 0 &&
                            fformation.type_formation.map((data) => <td value={data?.nb_effectif}>
                                {data?.nb_effectif} </td>)}
                        </tr>
                        <tr>
                        {Object.keys(fformation.type_formation).length !== 0 &&
                            fformation.type_formation.map((data) => <td value={data?.montant}> {data?.montant} </td>)}
                        </tr>
                    </tbody>
                    </table>
                </div>
                ) : (
                <CircularProgress sx={{ marginTop: '25px' }} disableShrink />
            )}
            </div>}
            
        </>
    );
};

export default TableRHS;