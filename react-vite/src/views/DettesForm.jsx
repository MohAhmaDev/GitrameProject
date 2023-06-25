import React, { useEffect, useState } from "react";
import { useForm, Controller } from "react-hook-form";
import { useDisplayContext } from "../contexts/DisplayContext";
import { useStateContext } from "../contexts/ContextProvider";
import axiosClient from "../axios-client";


import { 
  TextField,
  Zoom,
  Box, 
  FormControlLabel,
  FormControl, 
  InputLabel, 
  Select, 
  MenuItem, 
  useMediaQuery,
  FormLabel,
  Button,
  CircularProgress
} from '@mui/material';
import { AdapterDayjs } from '@mui/x-date-pickers/AdapterDayjs';
import dayjs from "dayjs";
import { LocalizationProvider } from '@mui/x-date-pickers/LocalizationProvider';
import { DatePicker } from '@mui/x-date-pickers/DatePicker';


import { yupResolver } from '@hookform/resolvers/yup';
import * as yup from "yup";
import { useNavigate, useParams } from "react-router-dom";
import { useFormsContext } from "../contexts/FormsContext";

export default function DettesForm() {
 
  // selected?.debtor ? yup.string() : 
  const {id} = useParams()
  const [dette, setDette] = useState({
    intitule_projet: '',
    num_fact: '',
    num_situation: '',
    date_dettes: undefined,
    montant: null,
    debtor_type: '',
    creditor_type: '',
    debtor_id: null,
    creditor_id: null,
    observations: '',
    role: "",
    montant_encaissement: null,
  })
  const [show, setShow] = useState(false);
  const [selected, setSelected] = useState(null);
  const {filiales, getFiliales, setFiliales} = useDisplayContext()
  const {setNotification, filiale, role, user, fetchUser} = useStateContext()
  const { entreprises, getEntreprises } = useFormsContext()
  const navigate = useNavigate();
  const [loading, setLoading] = useState(false);




  useEffect(() => {
    getFiliales()
    getEntreprises()
    fetchUser();
  }, [role])



  const useScheme = yup.object({
    intitule_projet: yup.string().required('ce champ est obligatoire'),
    num_fact: yup.string().required('ce champ est obligatoire'),
    num_situation: yup.string().required('ce champ est obligatoire'),
    date_dettes: yup.date().required('le champs date est obligatoire'),
    montant: yup.number().required('ce champ est obligatoire'),
    debtor_type: filiale.id ? null : yup.string().required('le champs débiteur est obligatoire'),
    creditor_type: yup.string().required('le champ créditeur est obligatoire'),
    debtor_id: filiale.id ? null : yup.number().required(),
    creditor_id: yup.number().required()
  })
  const { handleSubmit, control, setError, reset, register, formState,
  formState: { errors } , setValue} = useForm({
    mode: "onChange",
    resolver: yupResolver(useScheme)
  });
  const {isSubmitting, isValid, isSubmitted, isSubmitSuccessful} = formState                   
  const isNonMobile = useMediaQuery("(min-width:600px)");


  function useFirm() {
    const [firmes, setFirmes] = useState({})
    const [checked, setChecked] = useState(false)    

    const Increment = (e) => {
      if (e === 'filiale') {
        setFirmes(filiales)
      } else {
        setFirmes(entreprises)
      }
      setChecked(true) 
    }
    return [firmes, checked, Increment]
  }

  const [firmesCreditor, checkedOne, IncrementCreditor] = useFirm()
  const [firmesDebtor, checkedTwo, IncrementDebtor] = useFirm()

  const handleChangeCreditor = (e) => {
    IncrementCreditor(e);
  };

  const handlChangeDebtor = (e) => {
    IncrementDebtor(e);
  }



  const onSubmit = (data) => {
    // console.log(data)
    if (dette.id) 
    {
      const updateDette = {
        intitule_projet: data.intitule_projet,
        num_fact: data.num_fact,
        num_situation: data.num_situation,
        date_dettes: dayjs(data.date_dettes).format("YYYY-MM-DD"),
        observations: data.observations,
        creditor_type: data.creditor_type,
        creditor_id: data.creditor_id,
        debtor_type: filiale.id ? "filiale" : data.debtor_type,
        debtor_id: filiale.id ? filiale.id : data.debtor_id,
        montant: data.montant,
        montant_encaissement: data.montant_encaissement,
        regler: data.regler
      }
      axiosClient.put(`/dettes/${dette.id}`, updateDette)
      .then(() => {
        //TODO show notification
        setNotification("dette was successfuly Update")
        navigate('/dettes')
      })
      .catch(err => {
        console.log(err)
        const response = err.response;
        if (response && response.status === 422) {
          setError('server', {
            message: response?.data.message
          })        
        }
      })
      console.log(updateDette)
    } 
    else 
    {
      const detteAdd = {
        intitule_projet: data.intitule_projet,
        num_fact: data.num_fact,
        num_situation: data.num_situation,
        date_dettes: dayjs(data.date_dettes).format("YYYY-MM-DD"),
        observations: data.observations,
        creditor_type:data.creditor_type,
        creditor_id:data.creditor_id,
        debtor_type: filiale.id ? "filiale" : data.debtor_type,
        debtor_id: filiale.id ? filiale.id : data.debtor_id,
        montant: data.montant,
      }
      axiosClient.post('/dettes', detteAdd)
      .then(() => {
        setNotification("la dette à bien été saisite")
        navigate('/dettes')
      })
      .catch((err) => {
        console.log(err)
        const response = err.response;
        setError('server', {
          message: response?.data.errors
        })       
      })
    }
  }

  const creditor = (
    <FormControl 
      variant="outlined" 
      sx={{ gridColumn: "span 2"}}
    >
      <InputLabel> Firme </InputLabel>
      <Select
        {...register('creditor_id')}
        value={dette.creditor_id}
        onChange={ev => setDette({...dette, creditor_id: ev.target.value})}
        label="Firme"
      >
      {checkedOne && firmesCreditor?.map(firme => (
        <MenuItem value={firme.id} key={firme.id}> {firme.name} </MenuItem>
      ))}
      </Select>
      {errors.creditor_id && <span style={{
          color: "#d32f2f",
          fontSize: "0.75em",
          textAlign: "left",
          fontWeight: "400"
      }}> {errors.creditor_id.message} </span>}
    </FormControl>
  );

  const debtor = (
    <FormControl 
      variant="outlined" 
      sx={{ gridColumn: "span 2"}}
    >
      <InputLabel> Firme </InputLabel>
      <Select
        {...register('debtor_id')}
        value={dette.debtor_id}
        onChange={ev => setDette({...dette, debtor_id: ev.target.value})}
        label="Firme"
      >
      {checkedTwo && firmesDebtor?.map(firme => (
        <MenuItem value={firme.id} key={firme.id}> {firme.name} </MenuItem>
      ))}
      </Select>
      {errors.debtor_id && <span style={{
          color: "#d32f2f",
          fontSize: "0.75em",
          textAlign: "left",
          fontWeight: "400"
      }}> {errors.debtor_id.message} </span>}
    </FormControl>    
  )
  

  const getDette = () => {
    setLoading(true)
    axiosClient.get(`dettes/${id}`)
    .then(({data}) => {
      setDette({
        id: id,
        intitule_projet: data.data.intitule_projet,
        num_fact: data.data.num_fact,
        num_situation: data.data.num_situation,
        date_dettes: dayjs(data.data.date_dettes),
        observations: data.data.observations,
        creditor_type: data.data.creditor_type,
        creditor_id: data.data.creditor_id,
        debtor_type: data.data.debtor_type,
        debtor_id: data.data.debtor_id,   
        role: data.data.role, 
        montant_encaissement: data.data.montant_encaissement,
        regler: data.data.regler      
      })
      reset({...data.data})
      setLoading(false)
    })
    .catch((err) => {
      console.log(err)
    })
}

if (id) {
  useEffect(() => {
    fetchUser()
    getDette()
    console.log(dette)
    if ((Object.keys(dette).length !== 0)) {
      handlChangeDebtor(dette.debtor_type)
      handleChangeCreditor(dette.creditor_type)
    }
  }, [dette?.id])
}

  return ( <>

    {(!loading) && 
    <div className="card animated fadeInDown">
      <Box m="20px">
        <form onSubmit={handleSubmit(onSubmit)}>
          <Box m="50px" display="grid" gridTemplateColumns="repeat(6, minmax(0, 1fr))"
            gap="20px" 
            sx={{ display: selected?.creditor ? 'none' : "grid" }}   
          >
            <FormLabel> oranisme: créditrice </FormLabel>
            <FormControlLabel
              variant="outlined" 
              sx={{ gridColumn: "span 3"}}
              control={      
              <FormControl fullWidth>
                <InputLabel> créditeur </InputLabel>
                <Select label="créditeur" 
                {...register('creditor_type')}
                value={dette.creditor_type}
                onChange={ev => {
                  setDette({...dette, creditor_type: ev.target.value});
                  handleChangeCreditor(ev.target.value);
                }}>
                  <MenuItem value="filiale"> filiale </MenuItem>
                  <MenuItem value="entreprise"> entreprise </MenuItem>
                </Select>
                {errors.creditor_type && <span style={{
                  color: "#d32f2f",
                  fontSize: "0.75em",
                  textAlign: "left",
                  fontWeight: "400"
                }}> {errors.creditor_type.message} </span>}   
              </FormControl>
              }
            />
            <Zoom
              in={checkedOne} style={{ transitionDelay: checkedOne ? '300ms' : '0ms' ,
              display: checkedOne ? null : 'none'}}>
              {creditor}
            </Zoom>
          </Box>
          {(role === "admin") && <Box m="50px" display="grid" gridTemplateColumns="repeat(6, minmax(0, 1fr))"
            gap="20px"    
            sx={{ display: selected?.debtor ? 'none' : "grid" }}   
          >
            <FormLabel> oranisme: débitrice </FormLabel>
            <FormControlLabel
              variant="outlined" 
              sx={{ gridColumn: "span 3"}}
              control={      
              <FormControl fullWidth>
                <InputLabel> débiteur </InputLabel>
                <Select label="débiteur" 
                {...register('debtor_type')}
                value={dette.debtor_type}
                onChange={ev => {
                  setDette({...dette, debtor_type: ev.target.value});
                  handlChangeDebtor(ev.target.value);
                }}>
                  <MenuItem value="filiale"> filiale </MenuItem>
                  <MenuItem value="entreprise"> entreprise </MenuItem>
                </Select>
                {errors.debtor_type && <span style={{
                  color: "#d32f2f",
                  fontSize: "0.75em",
                  textAlign: "left",
                  fontWeight: "400"
                }}> {errors.debtor_type.message} </span>}            
              </FormControl>
              }
            />
            <Zoom
              in={checkedTwo} style={{ transitionDelay: checkedTwo ? '300ms' : '0ms' ,
              display: checkedTwo ? null : 'none'}}>
              {debtor}
            </Zoom>
          </Box>}
          <Box 
            m="50px"
            display="grid" 
            gridTemplateColumns="repeat(6, minmax(0, 1fr))"
            gap="30px" >

              <h2 style={{ gridColumn: "span 6" }}> Information sur la dette </h2>
              <TextField
                {...register('intitule_projet')}
                value={dette.intitule_projet}
                onChange={ev => setDette({...dette, intitule_projet: ev.target.value})}
                label="Intitule Projet"
                variant="outlined"
                sx={{ gridColumn: "span 2" }}
                error={errors.intitule_projet ? true : false}
                helperText={errors.intitule_projet && errors.intitule_projet.message}
              />
              <TextField
                {...register('num_fact')}
                value={dette.num_fact}
                onChange={ev => setDette({...dette, num_fact: ev.target.value})}
                label="Numero Facture"
                variant="outlined"
                sx={{ gridColumn: "span 2" }}
                error={errors.num_fact ? true : false}
                helperText={errors.num_fact && errors.num_fact.message}
              />
              <TextField
                {...register('num_situation')}
                value={dette.num_situation}
                onChange={ev => setDette({...dette, num_situation: ev.target.value})}
                label="Numero Situation"
                variant="outlined"
                sx={{ gridColumn: "span 2" }}
                error={errors.num_situation ? true : false}
                helperText={errors.num_situation && errors.num_situation.message}            
              />
              <Controller
                control={control}
                name="date_dettes"
                render={({ field: { onChange } }) => (
                  <LocalizationProvider dateAdapter={AdapterDayjs}>
                  <DatePicker
                    sx={{ gridColumn: "span 2" }}
                    label="date de la mois de dette" 
                    format="DD/MM/YYYY"
                    value={dette?.date_dettes}
                    onChange={ev => {
                      onChange(ev);
                      setDette({...dette, date_dettes: ev});
                    }}
                    slotProps={{
                      textField: {
                        error: errors.date_dettes ? true : false,
                        helperText: errors.date_dettes?.message 
                      }
                    }}
                  />
                </LocalizationProvider>
                )}
              />
              <TextField
                type="number"
                {...register('montant')}
                value={dette.montant}
                onChange={ev => setDette({...dette, montant: ev.target.value})}
                label="montant"
                variant="outlined"
                sx={{ gridColumn: "span 2" }}
                error={errors.montant ? true : false}
                helperText={errors.montant?.message}
              />
              {(dette?.id && !dette?.regler) && <TextField
                type="number"
                {...register('montant_encaissement')}
                value={dette.montant_encaissement}
                onChange={ev => setDette({...dette, montant_encaissement: ev.target.value})}
                label="montant_encaissement"
                variant="outlined"
                sx={{ gridColumn: "span 2" }}
              />}
              <TextField
                type="text"
                {...register('observations')}
                value={dette.observations}
                onChange={ev => setDette({...dette, observations: ev.target.value})}
                label="observations"
                variant="outlined"
                sx={{ gridColumn: "span 2" }}
              />

            </Box>
            <Box display="flex" justifyContent="end" mt="20px">
              <Button disabled={isSubmitting} type="submit" color="success" variant="contained">
               {id ? "Modifier la" : "créer une nouvelle" }  dette
              </Button>
            </Box>
          </form>
      </Box>
      {errors?.server?.message &&
          <div className="alert">
            {errors &&
              <p>{ errors?.server?.message }</p>
            }
          </div>}
    </div>}
    {loading && <CircularProgress disableShrink /> }
  </>);
}
