import React, { useEffect, useState } from "react";
import { useForm, Controller } from "react-hook-form";

import { AdapterDayjs } from '@mui/x-date-pickers/AdapterDayjs';
import { DesktopDatePicker, DatePicker, LocalizationProvider } from "@mui/x-date-pickers";
import { 
  FormControl,
  InputLabel,
  Select,
  MenuItem,
  Box,
  useMediaQuery,
  Button,
  TextField,
  RadioGroup,
  Radio,
  FormControlLabel,
  FormLabel,
  CircularProgress
} from "@mui/material";

import { yupResolver } from '@hookform/resolvers/yup';
import * as yup from "yup";
import axiosClient from "../axios-client";
import { useNavigate, useParams } from "react-router-dom";
import dayjs from "dayjs";
import { useStateContext } from "../contexts/ContextProvider";
import { useDisplayContext } from "../contexts/DisplayContext";

export default function EmployeForm() {

  const {id} = useParams();
  const [employe, setEmploye] = useState({
    id : null,
    nom: '',
    prenom: '',
    fonction: '',
    sexe: '',
    date_naissance: undefined,
    date_recrutement: undefined,
    date_retraite: undefined,
    contract: '',
    temp_occuper: '',
    handicape: false,    
    categ_sociopro: '',
    observation: '',
    filiale_id: null,
    created_at: '',  
  })
  const {filiales, getFiliales, setFiliales} = useDisplayContext()
  const {setNotification, filiale, role, user, fetchUser} = useStateContext()

  const useScheme = yup.object({
    nom: yup.string().required("le champ nom est obligatoire"),
    prenom: yup.string().required("le champ prenom est obligatoire"),
    fonction: yup.string().required("le champ fonction est obligatoire"),
    sexe: yup.string().required("ce champ est obligatoire"),
    date_naissance: yup.date().required("le champs date de naissance est obligatoire"),
    date_recrutement: yup.date().required('le champ date de recrutement est obligatoire'),
    date_retraite: yup.date().required('le champ date de retraite est obligatoire'),
    temp_occuper: yup.string().required('ce champ est obligatoire'),
    contract: yup.string().required('le champs contract est obligatoire'),
    categ_sociopro: yup.string().required('veillez ajouter une valeur a ce champs'),
    handicape: yup.boolean().nullable()
  })
  const { handleSubmit, control, setError, reset, getValues, setValue, register, formState, formState: { errors } } = useForm({
    mode: "onChange",
    resolver: yupResolver(useScheme)
  });


  const {isSubmitting, isValid, isSubmitted, isSubmitSuccessful} = formState                   
  const isNonMobile = useMediaQuery("(min-width:600px)");
  const navigate = useNavigate();
  const [loading, setLoading] = useState(false);



  const onSubmit = (data) => {
    if (employe.id) 
    {
      console.log("data : ", data)
      const updateEmploye = {
        nom: data.nom,
        prenom: data.prenom,
        fonction: data.fonction,
        sexe: data.sexe,
        date_naissance: dayjs(data.date_naissance).format("YYYY-MM-DD"),
        date_recrutement: dayjs(data.date_recrutement).format("YYYY-MM-DD"),
        contract: data.contract,
        temp_occuper: data.temp_occuper,
        handicape: data.handicape === null ? true : data.handicape,
        categ_sociopro: data.categ_sociopro,
        date_retraite: dayjs(data.date_retraite).format("YYYY-MM-DD"),
        filiale_id: filiale.id ? filiale.id : data.filiale,
      }
      axiosClient.put(`/employes/${employe.id}`, updateEmploye)
      .then(() => {
        //TODO show notification
        setNotification("employe was successfuly Update")
        navigate('/employes')
      })
      .catch(err => {
        const response = err.response;
        if (response && response.status === 422) {
          setError('server', {
            message: response?.data.errors
          })        
        }
      })
    } 
    else 
    {
      const Add_employe = {
        nom: data.nom,
        prenom: data.prenom,
        fonction: data.fonction,
        sexe: data.sexe,
        date_naissance: dayjs(data.date_naissance).format("YYYY-MM-DD"),
        date_recrutement: dayjs(data.date_recrutement).format("YYYY-MM-DD"),
        contract: data.contract,
        temp_occuper: data.temp_occuper,
        handicape: data.handicape,
        categ_sociopro: data.categ_sociopro,
        date_retraite: dayjs(data.date_retraite).format("YYYY-MM-DD"),
        observation: data.observation,
        filiale_id: filiale.id ? filiale.id : data.filiale,
      }
      axiosClient.post('/employes', Add_employe)
      .then(() => {
        setNotification("l'utilisateur à bien été saiséer")
        navigate('/employes')
      })
      .catch((err) => {
        console.log(err)
        const response = err.response;
        if (response && response.status === 422) {
          setError('server', {
            message: response.response.data.errors
          })       
        }
      })
    }
  }


  const getEmploye = () => {
    setLoading(true)
    axiosClient.get(`employes/${id}`)
    .then(({data}) => {
      setEmploye({
        id : id,
        nom: data.data.nom,
        prenom: data.data.prenom,
        fonction: data.data.fonction,
        sexe: data.data.sexe,
        date_naissance: dayjs(data.data.date_naissance),
        date_recrutement: dayjs(data.data.date_recrutement),
        date_retraite: dayjs(data.data.date_retraite),
        contract: data.data.contract,
        temp_occuper: data.data.temp_occuper,
        handicape: data.data.handicape === 1 ? true : false,    
        categ_sociopro: data.data.categ_sociopro,
        observation: data.data.observation,
        filiale_id: data.data.filiale_id,
        created_at: data.data.created_at,          
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
      getEmploye()
      console.log(employe)
    }, [])
  }

  useEffect(() => {
    getFiliales()
    fetchUser()
  }, [])


  return (
    <>
      {!loading && <div className="card animated fadeInDown">
        <Box m="20px">
          <form onSubmit={handleSubmit(onSubmit)}>
            <Box 
              display="grid" 
              gap="30px" 
              gridTemplateColumns="repeat(6, minmax(0, 1fr))"
              sx={{
                  "& > div": {gridColumn: isNonMobile ? undefined : "span 4"},    
              }}>

            <TextField
              label="Nom"
              {...register("nom")}
              onChange={ev => setEmploye({...employe, nom: ev.target.value})}
              value={employe.nom}
              variant="outlined"
              sx={{ gridColumn: "span 2" }}
              error={errors.nom ? true : false}
              helperText={errors.nom && errors.nom?.message}
            />
            <TextField
              label="Prénom"
              {...register("prenom")}
              value={employe.prenom}
              variant="outlined"
              sx={{ gridColumn: "span 2" }}
              onChange={ev => setEmploye({...employe, prenom: ev.target.value})}
              error={errors.prenom ? true : false}
              helperText={errors.prenom && errors.prenom?.message}
            />

            <TextField
              label="Fonction"
              value={employe.fonction}
              variant="outlined"
              sx={{ gridColumn: "span 2" }}
              onChange={ev => setEmploye({...employe, fonction: ev.target.value})}
              {...register("fonction")}
              error={errors.fonction ? true : false}
              helperText={errors.fonction && errors.fonction?.message}
            />

            <Controller
              control={control}
              name="date_naissance"
              render={({ field: { onChange } }) => (
                <LocalizationProvider dateAdapter={AdapterDayjs}>
                  <DatePicker
                    label={"date de naissance"} 
                    sx={{ gridColumn: "span 2" }}
                    format="DD/MM/YYYY"
                    value={employe?.date_naissance}
                    onChange={(event) => {  
                      onChange(event)
                      setEmploye({...employe, date_naissance: event})
                    }}
                    slotProps={{
                      textField: {
                        error: errors.date_naissance ? true : false,
                        helperText: errors.date_naissance?.message 
                      }
                    }}
                  />
                </LocalizationProvider>
              )}
            />
            <LocalizationProvider dateAdapter={AdapterDayjs}>
              <DatePicker
                defaultValue={undefined}
                label={"date_recrutement"}
                {...register('date_recrutement')}
                value={employe.date_recrutement}
                sx={{ gridColumn: "span 2" }}
                format="DD/MM/YYYY"
                onChange={(event) => { 
                  setEmploye({...employe, date_recrutement: event})
                }}
                slotProps={{
                  textField: {
                    error: errors.date_recrutement ? true : false,
                    helperText: errors.date_recrutement?.message 
                  }
                }}
              />
            </LocalizationProvider>

            <LocalizationProvider dateAdapter={AdapterDayjs}>
              <DatePicker
                defaultValue={undefined}
                label={"date de retraite"}
                {...register('date_retraite')}
                value={employe.date_retraite}
                sx={{ gridColumn: "span 2" }}
                onChange={(event) => { 
                  setEmploye({...employe, date_retraite: event})
                }}
                format="DD/MM/YYYY"
                slotProps={{
                  textField: {
                    error: errors.date_retraite ? true : false,
                    helperText: errors.date_retraite?.message 
                  }
                }}
              />
            </LocalizationProvider>

            <FormControl variant="outlined" 
              sx={{ gridColumn: "span 2" }}>
              <InputLabel id="demo-simple-select-label"> Sexe </InputLabel>
              <Select
                label="Sexe"
                value={employe.sexe}
                {...register("sexe")}
                onChange={(event) => { setEmploye({...employe, sexe: event?.target?.value}) }}
                error={errors.sexe ? true : false}
              >
                <MenuItem value="Femme"> Femme </MenuItem>
                <MenuItem value="Homme"> Homme </MenuItem>
              </Select>
              {errors.sexe && <span style={{
                color: "#d32f2f",
                fontSize: "0.75em",
                textAlign: "left",
                fontWeight: "400"
              }}> {errors.sexe?.message} </span>}
            </FormControl>

            <FormControl variant="outlined" 
              sx={{ gridColumn: "span 2" }}>
              <InputLabel> Temp Occuper </InputLabel>
              <Select
                value={employe.temp_occuper}
                label="Temp Occuper"
                defaultValue={""}
                {...register("temp_occuper")}
                onChange={(event) => { setEmploye({...employe, temp_occuper: event?.target?.value}) }}
                error={errors.temp_occuper ? true : false}

              >
                <MenuItem value="Temps plein"> Temps plein </MenuItem>
                <MenuItem value="Temps partiel"> Temps partiel </MenuItem>
              </Select>
              {errors.temp_occuper && <span style={{
                color: "#d32f2f",
                fontSize: "0.75em",
                textAlign: "left",
                fontWeight: "400"
              }}> {errors.temp_occuper?.message} </span>}
            </FormControl>

            <FormControl variant="outlined" 
              sx={{ gridColumn: "span 2" }}>
              <InputLabel> contract </InputLabel>
              <Select
                value={employe.contract}
                label="contract"
                {...register("contract")}
                onChange={(event) => { setEmploye({...employe, contract: event?.target?.value}) }}
                error={errors.contract ? true : false}
              >
                <MenuItem value="CDI"> CDI </MenuItem>
                <MenuItem value="CDD"> CDD </MenuItem>
              </Select>
              {errors.sexe && <span style={{
                color: "#d32f2f",
                fontSize: "0.75em",
                textAlign: "left",
                fontWeight: "400"
              }}> {errors.contract?.message} </span>}

            </FormControl>

            <TextField
              value={employe.categ_sociopro}
              label="Catégorie socio profetionnelle"
              variant="outlined"
              {...register("categ_sociopro")}
              sx={{ gridColumn: "span 2" }}
              onChange={ev => setEmploye({...employe, categ_sociopro: ev?.target?.value})}
              error={errors.categ_sociopro ? true : false}
              helperText={errors.categ_sociopro && errors.categ_sociopro?.message}
            />
            {!employe.id && <TextField
              value={employe.observation}
              label="Observation"
              variant="outlined"
              {...register("observation")}
              onChange={ev => setEmploye({...employe, observation: ev?.target?.value})}
              sx={{ gridColumn: "span 2" }}
            />}

            <RadioGroup 
              value={employe?.handicape ? employe?.handicape : false}
              {...register("handicape")}
              sx={{ gridColumn: "span 2" }}
              onChange={ev => setEmploye({...employe, handicape: ev?.target?.value})}
            >
              <FormControlLabel
                value={false}
                control={<Radio value={false}/>}
                label="non-handicape"
              />
              <FormControlLabel
                value={true}
                control={<Radio value={true}/>}
                label="handicape"
              />
              {errors.handicape && <span style={{
                color: "#d32f2f",
                fontSize: "0.75em",
                textAlign: "left",
                fontWeight: "400"
              }}> {errors.handicape?.message} </span>}
            </RadioGroup>

            {(role === 'admin') && 
            <FormControl variant="outlined" sx={{ width: "300px" }}>
              { <InputLabel id="demo-simple-select-label"> filiale  </InputLabel>}
              <Select
                value={employe.filiale_id}
                {...register("filiale_id")}
                sx={{ gridColumn: "span 2" }}
                label="filiale"
                onChange={ev => setEmploye({...employe, filiale_id: ev?.target?.value})}
              >
                {(Object.keys(filiales).length !== 0) && filiales?.map(filiale => (
                  <MenuItem value={filiale.id} key={filiale.id}> {filiale.name} </MenuItem>
                ))}
              </Select>
            </FormControl>
            }      
            </Box>   
            <Box display="flex" justifyContent="end" mt="20px">
              <Button  type="submit" color="success" variant="contained">
                  {id ? "Update" : "Create New" }  Employe
              </Button>
            </Box>
          </form> 
        </Box>
        {errors?.server?.message &&
          <div className="alert">
            {Object.keys(errors?.server?.message).map(key => (
              <p>{errors?.server?.message[key][0]}</p>
            ))}
          </div>}
      </div>}
      {loading && <CircularProgress disableShrink /> }
    </>
  )
}
